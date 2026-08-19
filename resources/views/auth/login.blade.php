@extends('layouts.app')

@section('title', 'Вхід')

@section('content')
    <div class="max-w-sm mx-auto bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h1 class="text-xl font-semibold mb-6">Вхід</h1>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Пароль</label>
                <input type="password" id="password" name="password" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                Запам'ятати мене
            </label>
            <button type="submit" class="w-full rounded-lg bg-black text-white font-medium py-2.5 hover:bg-slate-800 transition">
                Увійти
            </button>
        </form>
    </div>
@endsection
