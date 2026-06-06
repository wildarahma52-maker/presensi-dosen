<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request, GoogleSheetService $sheet)
    {
        try {
            $presensi = collect($sheet->getRowsAsAssoc('presensi_dosen'));
            $dosen = collect($sheet->getRowsAsAssoc('dosen'));
            $jadwal = collect($sheet->getRowsAsAssoc('jadwal_mengajar'));
            $matkul = collect($sheet->getRowsAsAssoc('mata_kuliah'));
        } catch (\RuntimeException $exception) {
            return view('admin.presensi.index', [
                'rows' => [],
                'dosen' => [],
                'filters' => $request->all(),
            ])->with('error', $exception->getMessage());
        }

        if ($request->filled('tanggal')) {
            $presensi = $presensi->where('tanggal', $request->tanggal);
        }

        if ($request->filled('dosen_id')) {
            $presensi = $presensi->where('dosen_id', $request->dosen_id);
        }

        if ($request->filled('status')) {
            $presensi = $presensi->where('status_presensi', $request->status);
        }

        $rows = $presensi->map(function (array $row) use ($dosen, $jadwal, $matkul) {
            $jadwalRow = $jadwal->firstWhere('jadwal_id', $row['jadwal_id'] ?? '');
            $matkulRow = $matkul->firstWhere('matkul_id', $jadwalRow['matkul_id'] ?? '');
            $dosenRow = $dosen->firstWhere('dosen_id', $row['dosen_id'] ?? '');

            return $row + [
                'nama_dosen' => $dosenRow['nama_dosen'] ?? '-',
                'nama_mk' => $matkulRow['nama_mk'] ?? '-',
                'kelas' => $jadwalRow['kelas'] ?? '-',
            ];
        })->values()->all();

        return view('admin.presensi.index', [
            'rows' => $rows,
            'dosen' => $dosen->all(),
            'filters' => $request->all(),
        ]);
    }
}
