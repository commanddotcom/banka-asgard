@extends('layouts.admin')

@section('title', 'Донори')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Донори</h1>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mb-6">
        <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label for="bank_id" class="block text-sm font-medium text-slate-700 mb-1">Банка</label>
                <select id="bank_id" name="bank_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                    @foreach ($banks as $b)
                        <option value="{{ $b->id }}" @selected($bank && $bank->id === $b->id)>{{ $b->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="period" class="block text-sm font-medium text-slate-700 mb-1">Місяць</label>
                <input type="month" id="period" name="period" value="{{ $period->format('Y-m') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label for="source" class="block text-sm font-medium text-slate-700 mb-1">Джерело</label>
                <select id="source" name="source"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="" @selected($source === null)>Усі</option>
                    <option value="admin" @selected($source === 'admin')>Додані адміном</option>
                    <option value="donation" @selected($source === 'donation')>Додані донатом</option>
                </select>
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="amount_from" class="block text-sm font-medium text-slate-700 mb-1">Сума від</label>
                    <input type="number" id="amount_from" name="amount_from" min="0" step="0.01" value="{{ $amountFrom }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                </div>
                <div class="flex-1">
                    <label for="amount_to" class="block text-sm font-medium text-slate-700 mb-1">до</label>
                    <input type="number" id="amount_to" name="amount_to" min="0" step="0.01" value="{{ $amountTo }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-black text-white text-sm font-medium px-4 py-2.5 hover:bg-slate-800 transition">
                Застосувати
            </button>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mb-6">
        <h2 class="text-sm font-semibold mb-2">Завантажити список донорів (CSV)</h2>
        <p class="text-xs text-slate-500 mb-4">
            По одному донору на рядок у форматі
            <code class="bg-slate-100 px-1 rounded">Ім'я;4_цифри_номера_телефону;очікувана_сума</code>,
            розділювач — крапка з комою. Сума — необов'язкова (можна лишити рядок без неї). Наприклад:
        </p>
        <pre class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 mb-4 text-slate-600">Іван Петренко;1234;1500
Дмитро Іванов;3134;3134.50
Оксана Ковальчук;5678</pre>
        <p class="text-xs text-slate-500 mb-4">
            Список застосується до банки та місяця, обраних вище. Імена, яких ще немає у
            «Списку імен», буде додано туди автоматично. Якщо донор із таким іменем й номером
            вже є в цьому місяці — його очікувана сума оновиться.
        </p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.donors.import') }}" enctype="multipart/form-data" class="flex items-end gap-3">
            @csrf
            <input type="hidden" name="bank_id" value="{{ $bank?->id }}">
            <input type="hidden" name="period" value="{{ $period->format('Y-m') }}">
            <div class="flex-1">
                <label for="csv_file" class="block text-sm font-medium text-slate-700 mb-1">CSV файл</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                       class="w-full text-sm">
            </div>
            <button type="submit" class="rounded-lg bg-black text-white text-sm font-medium px-4 py-2.5 hover:bg-slate-800 transition">
                Завантажити
            </button>
        </form>
    </div>

    @if ($bank)
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-3">Ім'я</th>
                        <th class="px-4 py-3">Телефон</th>
                        <th class="px-4 py-3">Очікувана сума</th>
                        <th class="px-4 py-3">Фактично внесено</th>
                        <th class="px-4 py-3">Джерело</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($donors as $donor)
                        <tr 
                        @if ($donor->expected_amount !== null && $donor->actual_total && $donor->actual_total < $donor->expected_amount) 
                            class="bg-red-50"
                        @endif
                        >
                            <td class="px-4 py-3 font-medium">{{ $donor->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs">&bull;&bull;&bull;&bull;{{ $donor->phone_last4 }}</td>
                            <td class="px-4 py-3">
                                {{ $donor->expected_amount !== null ? number_format((float) $donor->expected_amount, 2, ',', ' ').' грн' : '—' }}
                            </td>
                            <td class="px-4 py-3 {{ $donor->actual_total > 0 ? 'text-emerald-600 font-medium' : 'text-amber-600' }}">
                                {{ number_format($donor->actual_total, 2, ',', ' ') }} грн
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $donor->added_by_admin ? 'Адмін' : 'Донат' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">Немає донорів за цими фільтрами.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($donors->isNotEmpty())
                    <tfoot>
                        <tr class="bg-slate-50 font-semibold">
                            <td class="px-4 py-3" colspan="3">Разом</td>
                            <td class="px-4 py-3">{{ number_format($grandTotal, 2, ',', ' ') }} грн</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @else
        <p class="text-sm text-slate-400">Спочатку додайте банку.</p>
    @endif
@endsection
