<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\MonobankException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Services\MonobankClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankController extends Controller
{
    private const CURRENCIES = [
        980 => 'UAH',
        840 => 'USD',
        978 => 'EUR',
    ];

    public function index(): View
    {
        $banks = Bank::query()->orderBy('order')->orderBy('title')->get();

        return view('admin.banks.index', ['banks' => $banks]);
    }

    public function create(): View
    {
        return view('admin.banks.create');
    }

    public function store(BankRequest $request): RedirectResponse
    {
        Bank::create($request->validated());

        return redirect()->route('admin.banks.index')->with('status', 'Банку додано.');
    }

    public function edit(Bank $bank): View
    {
        return view('admin.banks.edit', ['bank' => $bank]);
    }

    public function update(BankRequest $request, Bank $bank): RedirectResponse
    {
        $data = $request->validated();

        // Keep the existing token if the field is left blank on edit.
        if ($data['token'] === null) {
            $data['token'] = $bank->token;
        }

        // The wallet is locked once chosen — changing the token afterwards
        // never touches it.
        if (filled($bank->account_id)) {
            $data['account_id'] = $bank->account_id;
        }

        $bank->update($data);

        return redirect()->route('admin.banks.index')->with('status', 'Банку оновлено.');
    }

    /**
     * Look up the wallets (cards and jars) available for a Monobank token,
     * so the admin can pick which one this bank's account_id points at.
     */
    public function wallets(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        try {
            $client = new MonobankClient($request->string('token')->toString());
            $info = $client->clientInfo();
        } catch (MonobankException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $wallets = [];

        foreach ($info['accounts'] ?? [] as $account) {
            $wallets[] = ['id' => $account['id'], 'label' => $this->walletLabel($account, 'account')];
        }

        foreach ($info['jars'] ?? [] as $jar) {
            $wallets[] = ['id' => $jar['id'], 'label' => $this->walletLabel($jar, 'jar')];
        }

        return response()->json(['wallets' => $wallets]);
    }

    private function walletLabel(array $item, string $type): string
    {
        $currency = self::CURRENCIES[$item['currencyCode'] ?? 980] ?? (string) ($item['currencyCode'] ?? '');

        if ($type === 'jar') {
            $title = $item['title'] ?? 'Банка';

            return "{$title} ({$currency})";
        }

        $label = $item['maskedPan'][0] ?? ($item['iban'] ?? $item['id']);

        return "{$label} ({$currency})";
    }
}
