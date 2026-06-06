@extends('layouts.admin', ['title' => $row ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah'])

@section('content')
    <form method="POST" action="{{ $row ? route('admin.mata-kuliah.update', $row['matkul_id']) : route('admin.mata-kuliah.store') }}" class="grid gap-4 rounded-lg border bg-white p-5 md:grid-cols-2">
        @csrf
        @if ($row) @method('PUT') @endif
        @foreach ([['kode_mk','Kode MK'], ['nama_mk','Nama MK'], ['sks','SKS'], ['semester','Semester'], ['prodi','Prodi']] as [$name, $label])
            <label class="grid gap-1 text-sm">
                <span class="font-medium">{{ $label }}</span>
                <input name="{{ $name }}" value="{{ old($name, $row[$name] ?? '') }}" class="rounded-md border px-3 py-2">
            </label>
        @endforeach
        <div class="md:col-span-2 flex gap-3">
            <button class="rounded-md bg-slate-950 px-4 py-2 text-white">Simpan</button>
            <a href="{{ route('admin.mata-kuliah.index') }}" class="rounded-md border px-4 py-2">Batal</a>
        </div>
    </form>
@endsection
