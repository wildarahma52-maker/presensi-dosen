<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class RekapPresensiController extends Controller
{
    public function index(Request $request, GoogleSheetService $sheet)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        try {
            $dosen = collect($sheet->getRowsAsAssoc('dosen'))->where('status', 'aktif')->values();
            $jadwal = collect($sheet->getRowsAsAssoc('jadwal_mengajar'))->where('status', 'aktif');
            $presensi = collect($sheet->getRowsAsAssoc('presensi_dosen'))->filter(function (array $row) use ($bulan, $tahun) {
                $date = $row['tanggal'] ?? '';

                return $date !== ''
                    && (int) date('n', strtotime($date)) === $bulan
                    && (int) date('Y', strtotime($date)) === $tahun;
            });
        } catch (\RuntimeException $exception) {
            return view('admin.rekap.index', [
                'rows' => [],
                'bulan' => $bulan,
                'tahun' => $tahun,
            ])->with('error', $exception->getMessage());
        }

        $rows = $dosen->map(function (array $row) use ($jadwal, $presensi, $bulan, $tahun) {
            $totalJadwal = $jadwal->where('dosen_id', $row['dosen_id'])->count();
            $milikDosen = $presensi->where('dosen_id', $row['dosen_id']);
            $hadir = $milikDosen->where('status_presensi', 'hadir')->count();
            $terlambat = $milikDosen->where('status_presensi', 'terlambat')->count();
            $izin = $milikDosen->where('status_presensi', 'izin')->count();
            $alfa = max($totalJadwal - $hadir - $terlambat - $izin, $milikDosen->where('status_presensi', 'alfa')->count());
            $persentase = $totalJadwal > 0 ? round((($hadir + $terlambat) / $totalJadwal) * 100, 2) : 0;

            return [
                'nama_dosen' => $row['nama_dosen'],
                'bulan' => $bulan,
                'tahun' => $tahun,
                'total_jadwal' => $totalJadwal,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'alfa' => $alfa,
                'persentase_hadir' => $persentase,
            ];
        })->all();

        return view('admin.rekap.index', compact('rows', 'bulan', 'tahun'));
    }
}
