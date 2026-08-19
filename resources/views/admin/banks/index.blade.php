@extends('layouts.admin')

@section('title', 'Банки')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Банки</h1>
        <a href="{{ route('admin.banks.create') }}"
           class="rounded-lg bg-black text-white text-sm font-medium px-4 py-2 hover:bg-slate-800 transition">
            + Нова банка
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Назва</th>
                    <th class="px-4 py-3">IBAN</th>
                    <th class="px-4 py-3">ІПН</th>
                    <th class="px-4 py-3">Відстеження</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($banks as $bank)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $bank->order }}</td>
                        <td class="px-4 py-3 font-medium">{{ $bank->title }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $bank->iban }}</td>
                        <td class="px-4 py-3">{{ $bank->tax_id }}</td>
                        <td class="px-4 py-3">
                            @if ($bank->watch)
                                <span class="text-emerald-600">увімкнено</span>
                            @else
                                <span class="text-slate-400">вимкнено</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.banks.edit', $bank) }}" class="text-slate-500 hover:text-black">Редагувати</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">Немає банок.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
