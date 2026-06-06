@extends('layouts.admin', ['title' => $row ? 'Edit Jadwal' : 'Tambah Jadwal'])

@section('content')
    <form method="POST" action="{{ $row ? route('admin.jadwal.update', $row['jadwal_id']) : route('admin.jadwal.store') }}" class="grid gap-4 rounded-lg border bg-white p-5 md:grid-cols-2">
        @csrf
        @if ($row) @method('PUT') @endif
        <label class="grid gap-1 text-sm">
            <span class="font-medium">Dosen</span>
            <select name="dosen_id" class="rounded-md border px-3 py-2">
                @foreach ($dosen as $item)
                    <option value="{{ $item['dosen_id'] }}" @selected(old('dosen_id', $row['dosen_id'] ?? '') === $item['dosen_id'])>{{ $item['nama_dosen'] }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-1 text-sm">
            <span class="font-medium">Mata Kuliah</span>
            <select name="matkul_id" class="rounded-md border px-3 py-2">
                @foreach ($matkul as $item)
                    <option value="{{ $item['matkul_id'] }}" @selected(old('matkul_id', $row['matkul_id'] ?? '') === $item['matkul_id'])>{{ $item['nama_mk'] }}</option>
                @endforeach
            </select>
        </label>
        @foreach ([['kelas','Kelas'], ['hari','Hari'], ['jam_mulai','Jam Mulai'], ['jam_selesai','Jam Selesai'], ['ruangan','Ruangan']] as [$name, $label])
            <label class="grid gap-1 text-sm">
                <span class="font-medium">{{ $label }}</span>
                <input name="{{ $name }}" value="{{ old($name, $row[$name] ?? '') }}" class="rounded-md border px-3 py-2">
            </label>
        @endforeach
        <label class="grid gap-1 text-sm">
            <span class="font-medium">Status</span>
            <select name="status" class="rounded-md border px-3 py-2">
                @foreach (['aktif', 'nonaktif'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $row['status'] ?? 'aktif') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <div class="md:col-span-2 flex gap-3">
            <button class="rounded-md bg-slate-950 px-4 py-2 text-white">Simpan</button>
            <a href="{{ route('admin.jadwal.index') }}" class="rounded-md border px-4 py-2">Batal</a>
        </div>
    </form>
@endsection
