<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class JadwalMengajarController extends Controller
{
    public function index(GoogleSheetService $sheet)
    {
        return view('admin.jadwal.index', $this->viewData($sheet));
    }

    public function create(GoogleSheetService $sheet)
    {
        return view('admin.jadwal.form', $this->viewData($sheet) + ['row' => null]);
    }

    public function store(Request $request, GoogleSheetService $sheet)
    {
        $data = $this->validated($request);

        try {
            $sheet->append('jadwal_mengajar!A:I', [
                $sheet->generateId('JDW'),
                $data['dosen_id'],
                $data['matkul_id'],
                $data['kelas'],
                $data['hari'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $data['ruangan'],
                $data['status'],
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(string $id, GoogleSheetService $sheet)
    {
        $row = $sheet->findByColumn('jadwal_mengajar', 'jadwal_id', $id);

        abort_if(! $row, 404);

        return view('admin.jadwal.form', $this->viewData($sheet) + ['row' => $row]);
    }

    public function update(Request $request, string $id, GoogleSheetService $sheet)
    {
        $data = $this->validated($request);

        try {
            $sheet->updateRowById('jadwal_mengajar', 'jadwal_id', $id, ['jadwal_id' => $id] + $data);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(string $id, GoogleSheetService $sheet)
    {
        try {
            $sheet->updateRowById('jadwal_mengajar', 'jadwal_id', $id, ['status' => 'nonaktif']);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Jadwal dinonaktifkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'dosen_id' => 'required',
            'matkul_id' => 'required',
            'kelas' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruangan' => 'required',
            'status' => 'required|in:aktif,nonaktif',
        ]);
    }

    private function viewData(GoogleSheetService $sheet): array
    {
        try {
            $rows = $sheet->getRowsAsAssoc('jadwal_mengajar');
            $dosen = $sheet->getRowsAsAssoc('dosen');
            $matkul = $sheet->getRowsAsAssoc('mata_kuliah');
        } catch (\RuntimeException $exception) {
            session()->flash('error', $exception->getMessage());
            $rows = $dosen = $matkul = [];
        }

        return compact('rows', 'dosen', 'matkul');
    }
}
