<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">
    <header class="border-b border-slate-200 bg-white">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-lg font-semibold">{{ config('app.name') }}</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('reports') }}"
                   class="{{ request()->routeIs('reports') ? 'text-black font-medium' : 'text-slate-500 hover:text-black' }}">
                    Звіти
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        <div class="max-w-2xl mx-auto px-4 py-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        {{ config('app.name') }} &copy; {{ date('Y') }}
    </footer>
</body>
</html>
