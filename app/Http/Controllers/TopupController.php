<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Bank;
use App\Models\DonorName;
use App\Models\Invoice;
use App\Services\NbuQr;
use Illuminate\View\View;

class TopupController extends Controller
{
    public function create(Bank $bank): View
    {
        $names = DonorName::query()->orderBy('name')->pluck('name');

        return view('topup.create', ['bank' => $bank, 'names' => $names]);
    }

    public function store(StoreInvoiceRequest $request, Bank $bank): \Illuminate\Http\RedirectResponse
    {
        $invoice = $bank->invoices()->create($request->validated());

        return redirect()->route('invoices.show', $invoice);
    }

    public function show(Invoice $invoice): View
    {
        $bank = $invoice->bank;

        $qrUrl = NbuQr::buildUrl(
            $bank->title,
            $bank->iban,
            $bank->tax_id,
            $invoice->purpose(),
            (float) $invoice->amount,
        );

        $qrSvg = NbuQr::renderSvg($qrUrl);

        return view('topup.show', [
            'invoice' => $invoice,
            'bank' => $bank,
            'qrSvg' => $qrSvg,
        ]);
    }
}
