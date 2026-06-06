@extends('layouts.admin', ['title' => 'Data Dosen'])

@section('content')

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Data Dosen
            </h1>
            <p class="text-sm text-slate-500">
                Kelola data dosen dan akun login dosen.
            </p>
        </div>

        <a href="{{ route('admin.dosen.create') }}"
           class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white">
            Tambah Dosen
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr>
                    <th class="p-3">NIDN</th>
                    <th class="p-3">Nama Dosen</th>
                    <th class="p-3">Username</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">No HP</th>
                    <th class="p-3">Prodi</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t hover:bg-slate-50">
                        <td class="p-3">
                            {{ $row['nidn'] ?? '-' }}
                        </td>

                        <td class="p-3 font-medium">
                            {{ $row['nama_dosen'] ?? '-' }}
                        </td>

                        <td class="p-3">
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs">
                                {{ $row['username'] ?? '-' }}
                            </span>
                        </td>

                        <td class="p-3">
                            {{ $row['email'] ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $row['no_hp'] ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $row['prodi'] ?? '-' }}
                        </td>

                        <td class="p-3">
                            @if(($row['status'] ?? '') === 'aktif')
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-3">

                                <a href="{{ route('admin.dosen.edit', $row['dosen_id']) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.dosen.destroy', $row['dosen_id']) }}"
                                      onsubmit="return confirm('Nonaktifkan dosen ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-800">
                                        Nonaktifkan
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"
                            class="p-10 text-center text-slate-500">
                            Belum ada data dosen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection