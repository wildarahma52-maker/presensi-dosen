<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GoogleSheetService $sheet)
    {
        try {
            $dosen = $sheet->getRowsAsAssoc('dosen');
            $matkul = $sheet->getRowsAsAssoc('mata_kuliah');
            $jadwal = $sheet->getRowsAsAssoc('jadwal_mengajar');
            $presensi = $sheet->getRowsAsAssoc('presensi_dosen');
        } catch (\RuntimeException $exception) {
            return view('admin.dashboard', [
                'stats' => [],
                'error' => $exception->getMessage(),
            ]);
        }

        $today = now()->toDateString();

        return view('admin.dashboard', [
            'stats' => [
                'Total Dosen' => collect($dosen)->where('status', 'aktif')->count(),
                'Mata Kuliah' => count($matkul),
                'Jadwal Aktif' => collect($jadwal)->where('status', 'aktif')->count(),
                'Presensi Hari Ini' => collect($presensi)->where('tanggal', $today)->count(),
            ],
            'error' => null,
        ]);
    }
}
