<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Donor;
use App\Models\DonorName;
use App\Models\Invoice;
use App\Services\DonorCsvImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DonorController extends Controller
{
    public function index(Request $request): View
    {
        $banks = Bank::query()->orderBy('title')->get();

        $bank = $banks->firstWhere('id', (int) $request->input('bank_id')) ?? $banks->first();

        $period = $this->resolvePeriod($request->input('period'));
        $periodStart = $period->copy()->startOfMonth();
        $periodEnd = $period->copy()->endOfMonth();

        $source = $request->input('source'); // 'admin' | 'donation' | null
        $amountFrom = $request->input('amount_from');
        $amountTo = $request->input('amount_to');

        $donors = collect();
        $grandTotal = 0.0;

        if ($bank) {
            $donors = Donor::query()
                ->where('bank_id', $bank->id)
                ->where('period', $periodStart->toDateString())
                ->when($source === 'admin', fn ($q) => $q->where('added_by_admin', true))
                ->when($source === 'donation', fn ($q) => $q->where('added_by_admin', false))
                ->orderBy('name')
                ->get();

            $totals = Invoice::query()
                ->where('bank_id', $bank->id)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$periodStart, $periodEnd])
                ->groupBy('name', 'phone_last4')
                ->selectRaw('name, phone_last4, SUM(amount) as total')
                ->get()
                ->keyBy(fn ($row) => $row->name.'|'.$row->phone_last4);

            $donors->each(function (Donor $donor) use ($totals) {
                $key = $donor->name.'|'.$donor->phone_last4;
                $donor->actual_total = (float) ($totals[$key]->total ?? 0);
            });

            if ($amountFrom !== null && $amountFrom !== '') {
                $donors = $donors->filter(fn (Donor $d) => $d->actual_total >= (float) $amountFrom)->values();
            }

            if ($amountTo !== null && $amountTo !== '') {
                $donors = $donors->filter(fn (Donor $d) => $d->actual_total <= (float) $amountTo)->values();
            }

            $grandTotal = $donors->sum('actual_total');
        }

        return view('admin.donors.index', [
            'banks' => $banks,
            'bank' => $bank,
            'period' => $period,
            'source' => $source,
            'amountFrom' => $amountFrom,
            'amountTo' => $amountTo,
            'donors' => $donors,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_id' => ['required', 'exists:banks,id'],
            'period' => ['required', 'date_format:Y-m'],
            'csv_file' => ['required', 'file'],
        ]);

        $rows = DonorCsvImporter::parse(
            file_get_contents($request->file('csv_file')->getRealPath())
        );

        $period = Carbon::createFromFormat('Y-m', $request->input('period'))->startOfMonth();
        $bankId = (int) $request->input('bank_id');

        DB::transaction(function () use ($rows, $bankId, $period) {
            foreach ($rows as $row) {
                DonorName::firstOrCreate(['name' => $row['name']]);

                Donor::updateOrCreate(
                    [
                        'bank_id' => $bankId,
                        'period' => $period->toDateString(),
                        'name' => $row['name'],
                        'phone_last4' => $row['phone_last4'],
                    ],
                    ['expected_amount' => $row['amount'], 'added_by_admin' => true]
                );
            }
        });

        return redirect()
            ->route('admin.donors.index', ['bank_id' => $bankId, 'period' => $period->format('Y-m')])
            ->with('status', count($rows).' донорів завантажено.');
    }

    private function resolvePeriod(?string $value): Carbon
    {
        if ($value) {
            try {
                return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
            } catch (\Exception) {
                // fall through to default
            }
        }

        return now()->startOfMonth();
    }
}
