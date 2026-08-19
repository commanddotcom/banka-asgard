@extends('layouts.admin')

@section('title', 'Список імен')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Список імен</h1>
        <a href="{{ route('admin.names.create') }}"
           class="rounded-lg bg-black text-white text-sm font-medium px-4 py-2 hover:bg-slate-800 transition">
            + Нове ім'я
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Ім'я</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($names as $donorName)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $donorName->name }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.names.edit', $donorName) }}" class="text-slate-500 hover:text-black">Редагувати</a>
                            <form method="post" action="{{ route('admin.names.destroy', $donorName) }}" class="inline"
                                  onsubmit="return confirm('Видалити «{{ $donorName->name }}»?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-slate-400">Список порожній.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
