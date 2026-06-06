<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(GoogleSheetService $sheet)
    {
        return view('admin.mata-kuliah.index', ['rows' => $this->rows($sheet, 'mata_kuliah')]);
    }

    public function create()
    {
        return view('admin.mata-kuliah.form', ['row' => null]);
    }

    public function store(Request $request, GoogleSheetService $sheet)
    {
        $data = $request->validate([
            'kode_mk' => 'required',
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1',
            'prodi' => 'required',
        ]);

        try {
            $sheet->append('mata_kuliah!A:F', [
                $sheet->generateId('MK'),
                $data['kode_mk'],
                $data['nama_mk'],
                $data['sks'],
                $data['semester'],
                $data['prodi'],
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(string $id, GoogleSheetService $sheet)
    {
        $row = $sheet->findByColumn('mata_kuliah', 'matkul_id', $id);

        abort_if(! $row, 404);

        return view('admin.mata-kuliah.form', ['row' => $row]);
    }

    public function update(Request $request, string $id, GoogleSheetService $sheet)
    {
        $data = $request->validate([
            'kode_mk' => 'required',
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1',
            'prodi' => 'required',
        ]);

        try {
            $sheet->updateRowById('mata_kuliah', 'matkul_id', $id, ['matkul_id' => $id] + $data);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(string $id, GoogleSheetService $sheet)
    {
        try {
            $sheet->updateRowById('mata_kuliah', 'matkul_id', $id, ['nama_mk' => '[NONAKTIF] '.$id]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Mata kuliah ditandai nonaktif.');
    }

    private function rows(GoogleSheetService $sheet, string $name): array
    {
        try {
            return $sheet->getRowsAsAssoc($name);
        } catch (\RuntimeException $exception) {
            session()->flash('error', $exception->getMessage());

            return [];
        }
    }
}
