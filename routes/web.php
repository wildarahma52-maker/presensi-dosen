<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\JadwalMengajarController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\PresensiController as AdminPresensiController;
use App\Http\Controllers\Admin\RekapPresensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\JadwalController;
use App\Http\Controllers\Dosen\PresensiController as DosenPresensiController;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', function () {
    if (session('user')) {
        return session('user.role') === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dosen.dashboard');
    }

    return app(AuthController::class)->login();
})->name('login');

Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['check.login', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('dosen', DosenController::class)->parameters(['dosen' => 'id'])->except('show');
    Route::resource('mata-kuliah', MataKuliahController::class)->parameters(['mata-kuliah' => 'id'])->except('show');
    Route::resource('jadwal', JadwalMengajarController::class)->parameters(['jadwal' => 'id'])->except('show');
    Route::get('/presensi', [AdminPresensiController::class, 'index'])->name('presensi.index');
    Route::get('/rekap', [RekapPresensiController::class, 'index'])->name('rekap.index');
});

Route::middleware(['check.login', 'check.role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', DosenDashboardController::class)->name('dashboard');
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal/{jadwalId}/masuk', [DosenPresensiController::class, 'masuk'])->name('presensi.masuk');
    Route::post('/jadwal/{jadwalId}/keluar', [DosenPresensiController::class, 'keluar'])->name('presensi.keluar');
    Route::get('/presensi/riwayat', [DosenPresensiController::class, 'riwayat'])->name('presensi.riwayat');
});
