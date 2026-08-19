@extends('layouts.admin')

@section('title', 'Редагувати банку')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Редагувати «{{ $bank->title }}»</h1>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm max-w-lg">
        <form method="post" action="{{ route('admin.banks.update', $bank) }}" class="space-y-4">
            @include('admin.banks._form')
        </form>
    </div>
@endsection
