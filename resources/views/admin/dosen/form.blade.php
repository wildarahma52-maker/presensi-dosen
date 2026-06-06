@extends('layouts.admin', ['title' => $row ? 'Edit Dosen' : 'Tambah Dosen'])

@section('content')
    <form method="POST"
          action="{{ $row ? route('admin.dosen.update', $row['dosen_id']) : route('admin.dosen.store') }}"
          class="grid gap-4 rounded-lg border bg-white p-5 md:grid-cols-2">

        @csrf
        @if ($row)
            @method('PUT')
        @endif

        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold text-slate-900">Data Dosen</h2>
            <p class="text-sm text-slate-500">Lengkapi data identitas dosen.</p>
        </div>

        @foreach ([['nidn','NIDN'], ['nama_dosen','Nama Dosen'], ['email','Email'], ['no_hp','No HP'], ['prodi','Prodi']] as [$name, $label])
            <label class="grid gap-1 text-sm">
                <span class="font-medium">{{ $label }}</span>
                <input
                    name="{{ $name }}"
                    value="{{ old($name, $row[$name] ?? '') }}"
                    class="rounded-md border px-3 py-2"
                    required
                >
                @error($name)
                    <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </label>
        @endforeach

        <label class="grid gap-1 text-sm">
            <span class="font-medium">Status</span>
            <select name="status" class="rounded-md border px-3 py-2" required>
                @foreach (['aktif', 'nonaktif'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $row['status'] ?? 'aktif') === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>

        <div class="md:col-span-2 mt-4 border-t pt-4">
            <h2 class="text-lg font-semibold text-slate-900">Akun Login Dosen</h2>
            <p class="text-sm text-slate-500">
                Username dan password ini digunakan dosen untuk login ke sistem.
            </p>
        </div>

        <label class="grid gap-1 text-sm">
            <span class="font-medium">Username</span>
            <input
                type="text"
                name="username"
                value="{{ old('username', $user['username'] ?? '') }}"
                class="rounded-md border px-3 py-2"
                required
            >
            @error('username')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>

        <label class="grid gap-1 text-sm">
            <span class="font-medium">Password</span>
            <input
                type="password"
                name="password"
                class="rounded-md border px-3 py-2"
                {{ $row ? '' : 'required' }}
            >

            @if ($row)
                <span class="text-xs text-slate-500">
                    Kosongkan jika tidak ingin mengubah password.
                </span>
            @endif

            @error('password')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>

        <div class="md:col-span-2 flex gap-3 pt-3">
            <button class="rounded-md bg-slate-950 px-4 py-2 text-white">
                Simpan
            </button>

            <a href="{{ route('admin.dosen.index') }}"
               class="rounded-md border px-4 py-2">
                Batal
            </a>
        </div>
    </form>
@endsection