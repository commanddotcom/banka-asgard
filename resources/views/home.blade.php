@extends('layouts.app')

@section('title', 'Банки для поповнення')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Оберіть банку для поповнення</h1>

    @if ($banks->isEmpty())
        <p class="text-sm text-slate-500">Наразі немає доступних банок.</p>
    @else
        <div class="space-y-3">
            @foreach ($banks as $bank)
                <a href="{{ route('banks.topup', $bank) }}"
                   class="block bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-black transition">
                    <div class="font-medium">{{ $bank->title }}</div>
                    @if ($bank->description)
                        <p class="text-sm text-slate-500 mt-1">{{ $bank->description }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection
