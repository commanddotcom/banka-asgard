@extends('layouts.admin')

@section('title', 'Новий користувач')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Новий користувач</h1>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ім'я</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Пароль</label>
                <input type="password" id="password" name="password" minlength="8" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <button type="submit" class="w-full rounded-lg bg-black text-white font-medium py-2.5 hover:bg-slate-800 transition">
                Створити
            </button>
        </form>
    </div>
@endsection
