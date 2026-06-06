<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function masuk(Request $request, GoogleSheetService $sheet, string $jadwalId)
    {
        $dosenId = session('user.dosen_id');

        try {
            $jadwal = $sheet->findByColumn('jadwal_mengajar', 'jadwal_id', $jadwalId);

            if (! $jadwal || $jadwal['dosen_id'] !== $dosenId || $jadwal['status'] !== 'aktif') {
                return back()->with('error', 'Jadwal tidak valid untuk akun Anda.');
            }

            $today = now()->toDateString();
            $existing = collect($sheet->getRowsAsAssoc('presensi_dosen'))
                ->where('tanggal', $today)
                ->where('dosen_id', $dosenId)
                ->where('jadwal_id', $jadwalId)
                ->first();

            if ($existing) {
                return back()->with('error', 'Presensi masuk untuk jadwal ini sudah tercatat hari ini.');
            }

            $now = now();
            $status = $now->format('H:i') > substr($jadwal['jam_mulai'], 0, 5) ? 'terlambat' : 'hadir';

            $sheet->append('presensi_dosen!A:L', [
                $sheet->generateId('PRS'),
                $today,
                $dosenId,
                $jadwalId,
                $now->format('H:i:s'),
                '',
                $status,
                $request->input('keterangan', ''),
                $request->input('latitude', ''),
                $request->input('longitude', ''),
                $request->input('foto', ''),
                $now->toDateTimeString(),
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Presensi masuk berhasil disimpan.');
    }

    public function keluar(GoogleSheetService $sheet, string $jadwalId)
    {
        $dosenId = session('user.dosen_id');

        try {
            $presensi = collect($sheet->getRowsAsAssoc('presensi_dosen'))
                ->where('tanggal', now()->toDateString())
                ->where('dosen_id', $dosenId)
                ->where('jadwal_id', $jadwalId)
                ->first();

            if (! $presensi) {
                return back()->with('error', 'Presensi masuk belum ditemukan untuk jadwal ini.');
            }

            if (($presensi['jam_keluar'] ?? '') !== '') {
                return back()->with('error', 'Presensi keluar sudah tercatat.');
            }

            $sheet->updateRowById('presensi_dosen', 'presensi_id', $presensi['presensi_id'], [
                'jam_keluar' => now()->format('H:i:s'),
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Presensi keluar berhasil disimpan.');
    }

    public function riwayat(GoogleSheetService $sheet)
    {
        $dosenId = session('user.dosen_id');

        try {
            $presensi = collect($sheet->getRowsAsAssoc('presensi_dosen'))->where('dosen_id', $dosenId);
            $jadwal = collect($sheet->getRowsAsAssoc('jadwal_mengajar'));
            $matkul = collect($sheet->getRowsAsAssoc('mata_kuliah'));
        } catch (\RuntimeException $exception) {
            return view('dosen.presensi.riwayat', ['rows' => []])->with('error', $exception->getMessage());
        }

        $rows = $presensi->sortByDesc('tanggal')->map(function (array $row) use ($jadwal, $matkul) {
            $jadwalRow = $jadwal->firstWhere('jadwal_id', $row['jadwal_id'] ?? '');
            $mk = $matkul->firstWhere('matkul_id', $jadwalRow['matkul_id'] ?? '');

            return $row + [
                'nama_mk' => $mk['nama_mk'] ?? '-',
                'kelas' => $jadwalRow['kelas'] ?? '-',
            ];
        })->values()->all();

        return view('dosen.presensi.riwayat', ['rows' => $rows]);
    }
}
