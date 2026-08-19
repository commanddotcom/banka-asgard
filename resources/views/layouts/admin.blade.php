<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Адмінка') &middot; {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">
    <header class="border-b border-slate-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <span class="text-lg font-semibold">{{ config('app.name') }}</span>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('admin.invoices.index') }}"
                       class="{{ request()->routeIs('admin.invoices.*') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                        Рахунки
                    </a>
                    <a href="{{ route('admin.banks.index') }}"
                       class="{{ request()->routeIs('admin.banks.*') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                        Банки
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                        Користувачі
                    </a>
                    <a href="{{ route('admin.names.index') }}"
                       class="{{ request()->routeIs('admin.names.*') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                        Список імен
                    </a>
                    <a href="{{ route('admin.donors.index') }}"
                       class="{{ request()->routeIs('admin.donors.*') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                        Донори
                    </a>
                </nav>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-slate-500">{{ auth()->user()->email }}</span>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-black">Вийти</button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <div class="max-w-5xl mx-auto px-4 py-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
