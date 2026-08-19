@extends('layouts.app')

@section('title', 'Рахунок #' . $invoice->id)

@section('content')
    <a href="{{ route('banks.topup', $bank) }}" class="text-sm text-slate-500 hover:text-black">&larr; Назад</a>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mt-4 text-center">
        <p class="text-sm text-slate-500 mb-6">{{ $invoice->purpose() }}</p>

        <div class="mx-auto max-w-xs [&>svg]:w-full [&>svg]:h-auto">{!! $qrSvg !!}</div>

        <dl class="mt-6 text-sm text-left space-y-1">
            <div class="flex justify-between">
                <dt class="text-slate-500">Банка</dt>
                <dd class="font-medium">{{ $bank->title }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Сума</dt>
                <dd class="font-medium">{{ number_format((float) $invoice->amount, 2, ',', ' ') }} грн</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">IBAN</dt>
                <dd class="font-mono text-xs">{{ $bank->iban }}</dd>
            </div>
        </dl>

        <p class="text-xs text-slate-400 mt-6">
            Відскануйте QR-код у застосунку вашого банку, щоб здійснити переказ.
        </p>
    </div>
@endsection
