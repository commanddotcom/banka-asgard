@csrf
@isset($donorName)
    @method('PUT')
@endisset

<div>
    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ім'я *</label>
    <input type="text" id="name" name="name" value="{{ old('name', $donorName->name ?? '') }}" required autofocus
           class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
</div>

<button type="submit" class="w-full rounded-lg bg-black text-white font-medium py-2.5 hover:bg-slate-800 transition">
    Зберегти
</button>
