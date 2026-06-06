<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;

class DashboardController extends Controller
{
    public function __invoke(GoogleSheetService $sheet)
    {
        $dosenId = session('user.dosen_id');

        try {
            $jadwal = collect($sheet->getRowsAsAssoc('jadwal_mengajar'))->where('dosen_id', $dosenId);
            $presensi = collect($sheet->getRowsAsAssoc('presensi_dosen'))->where('dosen_id', $dosenId);
        } catch (\RuntimeException $exception) {
            return view('dosen.dashboard', [
                'stats' => [],
                'error' => $exception->getMessage(),
            ]);
        }

        return view('dosen.dashboard', [
            'stats' => [
                'Jadwal Aktif' => $jadwal->where('status', 'aktif')->count(),
                'Hadir' => $presensi->where('status_presensi', 'hadir')->count(),
                'Terlambat' => $presensi->where('status_presensi', 'terlambat')->count(),
                'Presensi Hari Ini' => $presensi->where('tanggal', now()->toDateString())->count(),
            ],
            'error' => null,
        ]);
    }
}
