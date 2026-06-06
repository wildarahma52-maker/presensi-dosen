<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;

class JadwalController extends Controller
{
    public function index(GoogleSheetService $sheet)
    {
        $dosenId = session('user.dosen_id');

        try {
            $jadwal = collect($sheet->getRowsAsAssoc('jadwal_mengajar'))
                ->where('dosen_id', $dosenId)
                ->where('status', 'aktif')
                ->values();
            $matkul = collect($sheet->getRowsAsAssoc('mata_kuliah'));
            $presensiHariIni = collect($sheet->getRowsAsAssoc('presensi_dosen'))
                ->where('dosen_id', $dosenId)
                ->where('tanggal', now()->toDateString());
        } catch (\RuntimeException $exception) {
            return view('dosen.jadwal.index', ['rows' => []])->with('error', $exception->getMessage());
        }

        $rows = $jadwal->map(function (array $row) use ($matkul, $presensiHariIni) {
            $mk = $matkul->firstWhere('matkul_id', $row['matkul_id'] ?? '');
            $presensi = $presensiHariIni->firstWhere('jadwal_id', $row['jadwal_id'] ?? '');

            return $row + [
                'nama_mk' => $mk['nama_mk'] ?? '-',
                'kode_mk' => $mk['kode_mk'] ?? '-',
                'presensi' => $presensi,
            ];
        })->all();

        return view('dosen.jadwal.index', ['rows' => $rows]);
    }
}
