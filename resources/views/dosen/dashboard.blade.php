@extends('layouts.dosen', ['title' => 'Dashboard Dosen'])

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $label => $value)
            <div class="rounded-lg bg-white p-5 shadow-sm border">
                <div class="text-sm text-slate-500">{{ $label }}</div>
                <div class="mt-2 text-3xl font-bold">{{ $value }}</div>
            </div>
        @endforeach
    </div>
@endsection
