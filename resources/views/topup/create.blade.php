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
            <div class="relative">
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ваше ім'я</label>
                <input type="text" id="name" name="name" maxlength="255" value="{{ old('name') }}" required
                       autocomplete="off"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                <ul id="name-suggestions"
                    class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-auto rounded-lg border border-slate-300 bg-white shadow-sm"></ul>
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

    <script>
        (() => {
            const names = @json($names);
            const input = document.getElementById('name');
            const list = document.getElementById('name-suggestions');

            function hide() {
                list.classList.add('hidden');
                list.innerHTML = '';
            }

            function render(matches) {
                list.innerHTML = '';
                matches.forEach((name) => {
                    const item = document.createElement('li');
                    item.textContent = name;
                    item.dataset.name = name;
                    item.className = 'px-3 py-2 cursor-pointer hover:bg-slate-100';
                    list.appendChild(item);
                });
                list.classList.remove('hidden');
            }

            input.addEventListener('input', () => {
                const query = input.value.trim().toLowerCase();

                if (query.length < 1) {
                    hide();
                    return;
                }

                const matches = names.filter((name) => name.toLowerCase().startsWith(query));

                if (matches.length === 0 || matches.length >= 10) {
                    hide();
                    return;
                }

                render(matches);
            });

            list.addEventListener('click', (event) => {
                const item = event.target.closest('[data-name]');
                if (!item) return;
                input.value = item.dataset.name;
                hide();
            });

            document.addEventListener('click', (event) => {
                if (event.target !== input && !list.contains(event.target)) {
                    hide();
                }
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') hide();
            });
        })();
    </script>
@endsection
