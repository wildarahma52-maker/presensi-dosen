<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        if (session()->has('user')) {
            return session('user.role') === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dosen.dashboard');
        }

        return view('auth.login');
    }
    public function authenticate(Request $request, GoogleSheetService $sheet)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $rows = $sheet->getRowsAsAssoc('users');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        }

        foreach ($rows as $user) {
            if (
                $user['username'] === $request->username &&
                $user['password'] === $request->password &&
                $user['status'] === 'aktif'
            ) {
                session(['user' => $user]);

                return $user['role'] === 'admin'
                    ? redirect('/admin/dashboard')
                    : redirect('/dosen/dashboard');
            }
        }

        return back()->with('error', 'Username, password, atau status akun tidak valid.')->withInput();
    }

    public function logout()
    {
        session()->forget('user');

        return redirect('/login');
    }
}
