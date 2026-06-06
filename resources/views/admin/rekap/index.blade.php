@extends('layouts.admin', ['title' => 'Rekap Presensi'])

@section('content')
    <form method="GET" class="grid gap-3 rounded-lg border bg-white p-4 md:grid-cols-3">
        <select name="bulan" class="rounded-md border px-3 py-2">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" @selected($bulan === $i)>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
            @endfor
        </select>
        <input type="number" name="tahun" value="{{ $tahun }}" class="rounded-md border px-3 py-2">
        <button class="rounded-md bg-slate-950 px-4 py-2 text-white">Tampilkan</button>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr><th class="p-3">Dosen</th><th class="p-3">Total Jadwal</th><th class="p-3">Hadir</th><th class="p-3">Terlambat</th><th class="p-3">Izin</th><th class="p-3">Alfa</th><th class="p-3">Persentase</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $row['nama_dosen'] }}</td>
                        <td class="p-3">{{ $row['total_jadwal'] }}</td>
                        <td class="p-3">{{ $row['hadir'] }}</td>
                        <td class="p-3">{{ $row['terlambat'] }}</td>
                        <td class="p-3">{{ $row['izin'] }}</td>
                        <td class="p-3">{{ $row['alfa'] }}</td>
                        <td class="p-3">{{ $row['persentase_hadir'] }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">Rekap belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
