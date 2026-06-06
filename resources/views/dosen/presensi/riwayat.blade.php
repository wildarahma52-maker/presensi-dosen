@extends('layouts.dosen', ['title' => 'Riwayat Presensi'])

@section('content')
    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr><th class="p-3">Tanggal</th><th class="p-3">Mata Kuliah</th><th class="p-3">Kelas</th><th class="p-3">Masuk</th><th class="p-3">Keluar</th><th class="p-3">Status</th><th class="p-3">Keterangan</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t">
                        <td class="p-3">{{ $row['tanggal'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['nama_mk'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['kelas'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['jam_masuk'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['jam_keluar'] ?: '-' }}</td>
                        <td class="p-3">{{ ucfirst($row['status_presensi'] ?? '-') }}</td>
                        <td class="p-3">{{ $row['keterangan'] ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">Riwayat belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
