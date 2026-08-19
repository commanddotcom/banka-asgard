@extends('layouts.admin')

@section('title', 'Рахунки')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Рахунки</h1>

        <form method="get" class="flex items-center gap-2 text-sm">
            <input type="hidden" name="only_paid" value="0">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="only_paid" value="1" @checked($onlyPaid)
                       onchange="this.form.submit()" class="rounded border-slate-300">
                Лише оплачені
            </label>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Банка</th>
                    <th class="px-4 py-3">Ім'я</th>
                    <th class="px-4 py-3">Телефон</th>
                    <th class="px-4 py-3">Сума</th>
                    <th class="px-4 py-3">Статус</th>
                    <th class="px-4 py-3">Коментарі</th>
                    <th class="px-4 py-3">Створено</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->id }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $invoice->bank->title }}</td>
                        <td class="px-4 py-3">{{ $invoice->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs">&bull;&bull;&bull;&bull;{{ $invoice->phone_last4 }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $invoice->amount, 2, ',', ' ') }} грн</td>
                        <td class="px-4 py-3">
                            @if ($invoice->isPaid())
                                <span class="text-emerald-600">Оплачено</span>
                            @else
                                <span class="text-amber-600">Не оплачено</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-slate-500 hover:text-black hover:underline">
                                {{ $invoice->comments_count }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $invoice->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            @unless ($invoice->isPaid())
                                <form method="post" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-slate-500 hover:text-black">Позначити оплаченим</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-slate-400">Немає рахунків.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
@endsection
