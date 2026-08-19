@extends('layouts.admin')

@section('title', 'Нова банка')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Нова банка</h1>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm max-w-lg">
        <form method="post" action="{{ route('admin.banks.store') }}" class="space-y-4">
            @include('admin.banks._form')
        </form>
    </div>
@endsection
