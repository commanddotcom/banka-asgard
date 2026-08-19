@extends('layouts.app')

@section('title', 'Поповнити ' . $bank->title)

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-black">&larr; Усі банки</a>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mt-4">
        <h1 class="text-xl font-semibold mb-2">Поповнити «{{ $bank->title }}»</h1>
        @if ($bank->description)
            <p class="text-sm text-slate-600 mb-6">{{ $bank->description }}</p>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('banks.invoices.store', $bank) }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ваше ім'я</label>
                <input type="text" id="name" name="name" list="names-list" maxlength="255" value="{{ old('name') }}" required
                       autocomplete="off"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                <datalist id="names-list">
                    @foreach ($names as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div>
                <label for="phone_last4" class="block text-sm font-medium text-slate-700 mb-1">Останні 4 цифри номера телефону</label>
                <input type="text" id="phone_last4" name="phone_last4" inputmode="numeric" pattern="\d{4}" maxlength="4"
                       value="{{ old('phone_last4') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-slate-700 mb-1">Сума, грн</label>
                <input type="number" id="amount" name="amount" min="1" step="0.01" value="{{ old('amount') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <button type="submit" class="w-full rounded-lg bg-black text-white font-medium py-2.5 hover:bg-slate-800 transition">
                Згенерувати QR
            </button>
        </form>
    </div>
@endsection
