@extends('layouts.admin', ['title' => 'Presensi Dosen'])

@section('content')
    <form method="GET" class="grid gap-3 rounded-lg border bg-white p-4 md:grid-cols-4">
        <input type="date" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}" class="rounded-md border px-3 py-2">
        <select name="dosen_id" class="rounded-md border px-3 py-2">
            <option value="">Semua dosen</option>
            @foreach ($dosen as $item)
                <option value="{{ $item['dosen_id'] }}" @selected(($filters['dosen_id'] ?? '') === $item['dosen_id'])>{{ $item['nama_dosen'] }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-md border px-3 py-2">
            <option value="">Semua status</option>
            @foreach (['hadir', 'terlambat', 'izin', 'alfa'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="rounded-md bg-slate-950 px-4 py-2 text-white">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr><th class="p-3">Tanggal</th><th class="p-3">Dosen</th><th class="p-3">Mata Kuliah</th><th class="p-3">Kelas</th><th class="p-3">Masuk</th><th class="p-3">Keluar</th><th class="p-3">Status</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t">
                        <td class="p-3">{{ $row['tanggal'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['nama_dosen'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['nama_mk'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['kelas'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['jam_masuk'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['jam_keluar'] ?: '-' }}</td>
                        <td class="p-3">{{ ucfirst($row['status_presensi'] ?? '-') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
