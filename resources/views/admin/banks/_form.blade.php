@csrf
@isset($bank)
    @method('PUT')
@endisset

<div>
    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Назва *</label>
    <input type="text" id="title" name="title" value="{{ old('title', $bank->title ?? '') }}" required
           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
</div>

<div>
    <label for="order" class="block text-sm font-medium text-slate-700 mb-1">Порядок відображення</label>
    <input type="number" id="order" name="order" step="1" value="{{ old('order', $bank->order ?? 0) }}"
           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
    <p class="text-xs text-slate-400 mt-1">Менше число — вище у списку.</p>
</div>

<div>
    <label for="iban" class="block text-sm font-medium text-slate-700 mb-1">IBAN *</label>
    <input type="text" id="iban" name="iban" value="{{ old('iban', $bank->iban ?? '') }}" required
           class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-black">
</div>

<div>
    <label for="tax_id" class="block text-sm font-medium text-slate-700 mb-1">ІПН *</label>
    <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id', $bank->tax_id ?? '') }}" required
           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
</div>

<div>
    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Опис</label>
    <textarea id="description" name="description" rows="3"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">{{ old('description', $bank->description ?? '') }}</textarea>
</div>

@php
    $walletLocked = isset($bank) && filled($bank->account_id);
@endphp

<div>
    <label for="token" class="block text-sm font-medium text-slate-700 mb-1">
        Токен Monobank
        @isset($bank)
            <span class="text-slate-400 font-normal">(залиште порожнім, щоб не змінювати)</span>
        @endisset
    </label>
    <div class="flex gap-2">
        <input type="text" id="token" name="token" value="{{ old('token') }}"
               placeholder="{{ isset($bank) && $bank->token ? '••••••••' : '' }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-black">
        @unless ($walletLocked)
            <button type="button" id="fetch-wallets"
                    class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm hover:border-black transition">
                Отримати гаманці
            </button>
        @endunless
    </div>
</div>

<div>
    <label for="account_id" class="block text-sm font-medium text-slate-700 mb-1">Гаманець Monobank</label>

    @if ($walletLocked)
        <input type="text" name="account_id" value="{{ $bank->account_id }}" readonly
               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-slate-500">
        <p class="text-xs text-slate-400 mt-1">Гаманець уже обрано. Зміна токена на нього не впливає.</p>
    @else
        <select id="account_id" name="account_id"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                disabled>
            <option value="">Спочатку отримайте список гаманців</option>
        </select>
        <p id="account_id_status" class="text-xs text-slate-400 mt-1"></p>
    @endif
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="hidden" name="watch" value="0">
    <input type="checkbox" id="watch" name="watch" value="1"
           @checked(old('watch', $bank->watch ?? false)) class="rounded border-slate-300">
    Автоматично перевіряти надходження (потрібен токен і обраний гаманець)
</label>

<button type="submit" class="w-full rounded-lg bg-black text-white font-medium py-2.5 hover:bg-slate-800 transition">
    Зберегти
</button>

@unless ($walletLocked)
    <script>
        (function () {
            const tokenInput = document.getElementById('token');
            const fetchButton = document.getElementById('fetch-wallets');
            const select = document.getElementById('account_id');
            const status = document.getElementById('account_id_status');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const walletsUrl = @json(route('admin.banks.wallets'));
            const oldSelected = @json(old('account_id'));

            fetchButton.addEventListener('click', function () {
                const token = tokenInput.value.trim();
                if (!token) {
                    status.textContent = 'Спочатку введіть токен.';
                    return;
                }

                select.disabled = true;
                select.innerHTML = '<option value="">Завантаження...</option>';
                status.textContent = '';
                fetchButton.disabled = true;

                fetch(walletsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ token: token }),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        fetchButton.disabled = false;

                        if (!result.ok) {
                            select.innerHTML = '<option value="">—</option>';
                            status.textContent = result.data.message || 'Не вдалося отримати список гаманців.';
                            return;
                        }

                        const wallets = result.data.wallets || [];
                        if (wallets.length === 0) {
                            select.innerHTML = '<option value="">Гаманців не знайдено</option>';
                            status.textContent = '';
                            return;
                        }

                        const options = wallets.map(function (wallet) {
                            return '<option value="' + wallet.id + '">' + wallet.label + '</option>';
                        });
                        select.innerHTML = '<option value="">— Оберіть гаманець —</option>' + options.join('');
                        select.disabled = false;
                        if (oldSelected) {
                            select.value = oldSelected;
                        }
                    })
                    .catch(function () {
                        fetchButton.disabled = false;
                        select.innerHTML = '<option value="">—</option>';
                        status.textContent = 'Помилка з’єднання з сервером.';
                    });
            });
        })();
    </script>
@endunless
