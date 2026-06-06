<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index(GoogleSheetService $sheet)
    {
        $dosen = $this->safeRows($sheet, 'dosen');
        $users = $this->safeRows($sheet, 'users');

        $userMap = collect($users)
            ->keyBy('dosen_id')
            ->toArray();

        foreach ($dosen as &$row) {
            $row['username'] = $userMap[$row['dosen_id']]['username'] ?? '-';
        }

        return view('admin.dosen.index', [
            'rows' => $dosen,
        ]);
    }

    public function create()
    {
        return view('admin.dosen.form', [
            'row' => null,
            'user' => null,
        ]);
    }

    public function store(Request $request, GoogleSheetService $sheet)
    {
        $data = $request->validate([
            'nidn' => 'required',
            'nama_dosen' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required',
            'prodi' => 'required',
            'status' => 'required|in:aktif,nonaktif',

            'username' => 'required',
            'password' => 'required|min:4',
        ]);

        try {
            $existingUser = $sheet->findByColumn('users', 'username', $data['username']);

            if ($existingUser) {
                return back()
                    ->with('error', 'Username sudah digunakan.')
                    ->withInput();
            }

            $dosenId = $sheet->generateId('DSN');
            $userId = $sheet->generateId('USR');

            $sheet->append('dosen!A:G', [
                $dosenId,
                $data['nidn'],
                $data['nama_dosen'],
                $data['email'],
                $data['no_hp'],
                $data['prodi'],
                $data['status'],
            ]);

            $sheet->append('users!A:G', [
                $userId,
                $data['nama_dosen'],
                $data['username'],
                $data['password'],
                'dosen',
                $dosenId,
                $data['status'],
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen dan akun login berhasil ditambahkan.');
    }

    public function edit(string $id, GoogleSheetService $sheet)
    {
        $row = $sheet->findByColumn('dosen', 'dosen_id', $id);
        $user = $sheet->findByColumn('users', 'dosen_id', $id);

        abort_if(!$row, 404);

        return view('admin.dosen.form', [
            'row' => $row,
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $id, GoogleSheetService $sheet)
    {
        $data = $request->validate([
            'nidn' => 'required',
            'nama_dosen' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required',
            'prodi' => 'required',
            'status' => 'required|in:aktif,nonaktif',

            'username' => 'required',
            'password' => 'nullable|min:4',
        ]);

        try {
            $user = $sheet->findByColumn('users', 'dosen_id', $id);

            $sheet->updateRowById('dosen', 'dosen_id', $id, [
                'dosen_id' => $id,
                'nidn' => $data['nidn'],
                'nama_dosen' => $data['nama_dosen'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'prodi' => $data['prodi'],
                'status' => $data['status'],
            ]);

            if ($user) {
                $userData = [
                    'id' => $user['id'],
                    'nama' => $data['nama_dosen'],
                    'username' => $data['username'],
                    'role' => 'dosen',
                    'dosen_id' => $id,
                    'status' => $data['status'],
                ];

                if (!empty($data['password'])) {
                    $userData['password'] = $data['password'];
                } else {
                    $userData['password'] = $user['password'] ?? '';
                }

                $sheet->updateRowById('users', 'dosen_id', $id, $userData);
            } else {
                $sheet->append('users!A:G', [
                    $sheet->generateId('USR'),
                    $data['nama_dosen'],
                    $data['username'],
                    $data['password'] ?: 'dosen123',
                    'dosen',
                    $id,
                    $data['status'],
                ]);
            }
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen dan akun login berhasil diperbarui.');
    }

    public function destroy(string $id, GoogleSheetService $sheet)
    {
        try {
            $sheet->updateRowById('dosen', 'dosen_id', $id, [
                'status' => 'nonaktif',
            ]);

            $sheet->updateRowById('users', 'dosen_id', $id, [
                'status' => 'nonaktif',
            ]);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Data dosen dan akun login dinonaktifkan.');
    }

    private function safeRows(GoogleSheetService $sheet, string $name): array
    {
        try {
            return $sheet->getRowsAsAssoc($name);
        } catch (\RuntimeException $exception) {
            session()->flash('error', $exception->getMessage());

            return [];
        }
    }
}