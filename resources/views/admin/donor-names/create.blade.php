@extends('layouts.admin')

@section('title', "Нове ім'я")

@section('content')
    <h1 class="text-xl font-semibold mb-6">Нове ім'я</h1>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm max-w-md">
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.names.store') }}" class="space-y-4">
            @include('admin.donor-names._form')
        </form>
    </div>
@endsection
