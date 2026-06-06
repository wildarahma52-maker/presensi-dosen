@extends('layouts.dosen', ['title' => 'Jadwal Mengajar'])

@section('content')
    <div class="grid gap-4">
        @forelse ($rows as $row)
            <div class="rounded-lg border bg-white p-5">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="font-semibold text-lg">{{ $row['kode_mk'] }} - {{ $row['nama_mk'] }}</div>
                        <div class="text-sm text-slate-600">{{ $row['hari'] }}, {{ $row['jam_mulai'] }} - {{ $row['jam_selesai'] }} | Kelas {{ $row['kelas'] }} | {{ $row['ruangan'] }}</div>
                        @if ($row['presensi'])
                            <div class="mt-2 text-sm text-emerald-700">Masuk {{ $row['presensi']['jam_masuk'] }}{{ $row['presensi']['jam_keluar'] ? ', keluar '.$row['presensi']['jam_keluar'] : '' }}.</div>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if (! $row['presensi'])
                            <form method="POST" action="{{ route('dosen.presensi.masuk', $row['jadwal_id']) }}">
                                @csrf
                                <button class="rounded-md bg-emerald-700 px-4 py-2 text-white">Presensi Masuk</button>
                            </form>
                        @elseif (! ($row['presensi']['jam_keluar'] ?? null))
                            <form method="POST" action="{{ route('dosen.presensi.keluar', $row['jadwal_id']) }}">
                                @csrf
                                <button class="rounded-md bg-slate-950 px-4 py-2 text-white">Presensi Keluar</button>
                            </form>
                        @else
                            <span class="rounded-md bg-slate-100 px-4 py-2 text-slate-600">Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border bg-white p-6 text-center text-slate-500">Jadwal aktif belum tersedia.</div>
        @endforelse
    </div>
@endsection
