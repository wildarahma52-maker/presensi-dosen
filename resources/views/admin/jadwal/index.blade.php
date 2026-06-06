@extends('layouts.admin', ['title' => 'Jadwal Mengajar'])

@section('content')
    @php
        $dosenMap = collect($dosen)->keyBy('dosen_id');
        $matkulMap = collect($matkul)->keyBy('matkul_id');
    @endphp
    <div class="flex justify-end">
        <a href="{{ route('admin.jadwal.create') }}" class="rounded-md bg-slate-950 px-4 py-2 text-white">Tambah Jadwal</a>
    </div>
    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr><th class="p-3">Dosen</th><th class="p-3">Mata Kuliah</th><th class="p-3">Kelas</th><th class="p-3">Hari</th><th class="p-3">Jam</th><th class="p-3">Ruangan</th><th class="p-3">Status</th><th class="p-3"></th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t">
                        <td class="p-3">{{ $dosenMap[$row['dosen_id']]['nama_dosen'] ?? $row['dosen_id'] ?? '-' }}</td>
                        <td class="p-3">{{ $matkulMap[$row['matkul_id']]['nama_mk'] ?? $row['matkul_id'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['kelas'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['hari'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['jam_mulai'] ?? '-' }} - {{ $row['jam_selesai'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['ruangan'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['status'] ?? '-' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.jadwal.edit', $row['jadwal_id']) }}" class="text-blue-700">Edit</a>
                            <form method="POST" action="{{ route('admin.jadwal.destroy', $row['jadwal_id']) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="ml-3 text-rose-700">Nonaktifkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-slate-500">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
