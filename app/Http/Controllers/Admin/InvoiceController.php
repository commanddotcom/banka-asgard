<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceCommentRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $onlyPaid = $request->boolean('only_paid', true);

        $invoices = Invoice::query()
            ->with('bank')
            ->withCount('comments')
            ->when($onlyPaid, fn ($query) => $query->paid())
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'onlyPaid' => $onlyPaid,
        ]);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['bank', 'comments.user']);

        return view('admin.invoices.show', ['invoice' => $invoice]);
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->markPaid();

        return back()->with('status', "Рахунок #{$invoice->id} позначено оплаченим.");
    }

    public function storeComment(StoreInvoiceCommentRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        return redirect()->route('admin.invoices.show', $invoice)->with('status', 'Коментар додано.');
    }
}
