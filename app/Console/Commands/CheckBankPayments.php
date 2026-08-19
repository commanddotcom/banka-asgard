<?php

namespace App\Console\Commands;

use App\Exceptions\MonobankException;
use App\Models\Bank;
use App\Models\Invoice;
use App\Models\User;
use App\Services\MonobankClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-bank-payments')]
#[Description('Poll Monobank statements for watched banks and mark matching invoices as paid')]
class CheckBankPayments extends Command
{
    private const UUID_PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    public function handle(): void
    {
        // Monobank allows only 1 request/minute per token. This run is a
        // single moment in time, so if several banks share a token, only
        // one of them can actually be checked here — the rest would just
        // hit our own throttle and fail every single run forever. Ordering
        // by last_checked_at (oldest/never-checked first) and taking the
        // first bank per token gives fair round-robin coverage across runs
        // instead of always starving the same bank.
        $banks = Bank::query()
            ->where('watch', true)
            ->whereNotNull('token')
            ->whereNotNull('account_id')
            ->orderBy('last_checked_at')
            ->get();

        $checkedTokens = [];

        foreach ($banks as $bank) {
            $tokenKey = sha1($bank->token);

            if (isset($checkedTokens[$tokenKey])) {
                continue;
            }

            $checkedTokens[$tokenKey] = true;

            $this->checkBank($bank);
        }
    }

    private function checkBank(Bank $bank): void
    {
        $this->info("Перевірка банки #{$bank->id} ({$bank->title})...");

        $to = now();
        $from = $bank->last_checked_at ?? now()->subDay();

        try {
            $client = new MonobankClient($bank->token);
            $transactions = $client->statement($from->timestamp, $to->timestamp, $bank->account_id);
        } catch (MonobankException $e) {
            $this->error("Банка #{$bank->id} ({$bank->title}): {$e->getMessage()}");

            return;
        }

        if (app()->environment('local')) {
            $this->line('Відповідь Monobank: '.json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        foreach ($transactions as $transaction) {
            $amountCents = (int) ($transaction['amount'] ?? 0);
            $comment = (string) ($transaction['comment'] ?? '');

            if ($amountCents <= 0 || $comment === '') {
                continue;
            }

            if (! preg_match_all('/#('.self::UUID_PATTERN.')/i', $comment, $matches)) {
                continue;
            }

            foreach ($matches[1] as $uuid) {
                $invoice = $bank->invoices()
                    ->unpaid()
                    ->where('uuid', strtolower($uuid))
                    ->first();

                if (! $invoice) {
                    continue;
                }

                $this->reconcileAmount($invoice, $amountCents);
                $invoice->markPaid();
                $this->info("Банка #{$bank->id}: рахунок #{$invoice->id} позначено оплаченим.");
            }
        }

        // last_checked_at isn't mass-assignable (by design — it's bookkeeping,
        // not an admin-editable field), so it needs forceFill() here.
        $bank->forceFill(['last_checked_at' => $to])->save();
    }

    /**
     * If the amount actually received doesn't match what the invoice was
     * created for, correct the invoice's amount to reality and leave a
     * comment explaining why — instead of marking it paid with a now-wrong
     * amount on record.
     */
    private function reconcileAmount(Invoice $invoice, int $actualAmountCents): void
    {
        $invoiceAmountCents = (int) round(((float) $invoice->amount) * 100);

        if ($invoiceAmountCents === $actualAmountCents) {
            return;
        }

        $oldAmount = $this->formatAmount((float) $invoice->amount);
        $newAmount = $this->formatAmount($actualAmountCents / 100);

        $invoice->update(['amount' => $actualAmountCents / 100]);

        $admin = User::find(1);

        if ($admin) {
            $invoice->comments()->create([
                'user_id' => $admin->id,
                'body' => "Після отримання даних з банку сума платежа була змінена з {$oldAmount} на {$newAmount}.",
            ]);
        }
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, ',', ' ');
    }
}
