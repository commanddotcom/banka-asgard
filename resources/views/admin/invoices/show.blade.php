@extends('layouts.admin')

@section('title', 'Рахунок #' . $invoice->id)

@section('content')
    <a href="{{ route('admin.invoices.index') }}" class="text-sm text-slate-500 hover:text-black">&larr; Усі рахунки</a>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mt-4 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Рахунок #{{ $invoice->id }}</h1>
            @if ($invoice->isPaid())
                <span class="text-emerald-600 text-sm">Оплачено</span>
            @else
                <span class="text-amber-600 text-sm">Не оплачено</span>
            @endif
        </div>

        <dl class="text-sm space-y-1">
            <div class="flex justify-between">
                <dt class="text-slate-500">Банка</dt>
                <dd class="font-medium">{{ $invoice->bank->title }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Ім'я</dt>
                <dd class="font-medium">{{ $invoice->name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Телефон</dt>
                <dd class="font-mono text-xs">&bull;&bull;&bull;&bull;{{ $invoice->phone_last4 }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Сума</dt>
                <dd class="font-medium">{{ number_format((float) $invoice->amount, 2, ',', ' ') }} грн</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Призначення</dt>
                <dd class="font-medium">{{ $invoice->purpose() }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Створено</dt>
                <dd class="text-slate-500">{{ $invoice->created_at->format('d.m.Y H:i') }}</dd>
            </div>
            @if ($invoice->paid_at)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Оплачено</dt>
                    <dd class="text-slate-500">{{ $invoice->paid_at->format('d.m.Y H:i') }}</dd>
                </div>
            @endif
        </dl>

        @unless ($invoice->isPaid())
            <form method="post" action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="mt-4">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:border-black transition">
                    Позначити оплаченим
                </button>
            </form>
        @endunless
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Коментарі ({{ $invoice->comments->count() }})</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.invoices.comments.store', $invoice) }}" class="mb-6">
            @csrf
            <textarea name="body" rows="3" placeholder="Залишити коментар..." required
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">{{ old('body') }}</textarea>
            <button type="submit" class="mt-2 rounded-lg bg-black text-white text-sm font-medium px-4 py-2 hover:bg-slate-800 transition">
                Додати коментар
            </button>
        </form>

        @forelse ($invoice->comments as $comment)
            <div class="border-t border-slate-100 py-3 first:border-t-0 first:pt-0">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                    <span class="font-medium text-slate-700">{{ $comment->user->name }}</span>
                    <span>{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <p class="text-sm whitespace-pre-line">{{ $comment->body }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">Коментарів ще немає.</p>
        @endforelse
    </div>
@endsection
