@extends('layouts.admin', ['title' => 'Mata Kuliah'])

@section('content')
    <div class="flex justify-end">
        <a href="{{ route('admin.mata-kuliah.create') }}" class="rounded-md bg-slate-950 px-4 py-2 text-white">Tambah Mata Kuliah</a>
    </div>
    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr><th class="p-3">Kode</th><th class="p-3">Nama</th><th class="p-3">SKS</th><th class="p-3">Semester</th><th class="p-3">Prodi</th><th class="p-3"></th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t">
                        <td class="p-3">{{ $row['kode_mk'] ?? '-' }}</td>
                        <td class="p-3 font-medium">{{ $row['nama_mk'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['sks'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['semester'] ?? '-' }}</td>
                        <td class="p-3">{{ $row['prodi'] ?? '-' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.mata-kuliah.edit', $row['matkul_id']) }}" class="text-blue-700">Edit</a>
                            <form method="POST" action="{{ route('admin.mata-kuliah.destroy', $row['matkul_id']) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="ml-3 text-rose-700">Nonaktifkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
