@extends('layouts.admin')

@section('title', 'Користувачі')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Користувачі</h1>
        <a href="{{ route('admin.users.create') }}"
           class="rounded-lg bg-black text-white text-sm font-medium px-4 py-2 hover:bg-slate-800 transition">
            + Новий користувач
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Ім'я</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Створено</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">Немає користувачів.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
