<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MandorPanenController;
use App\Http\Controllers\MandorKaryawanController;

// Redirect halaman utama ke login
Route::get('/', function () {
    return view('auth.login');
});

// Rute untuk login dan logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute setelah login untuk memilih kebun
Route::get('/pilih-kebun', [AuthController::class, 'showKebunSelection'])->name('pilih.kebun');
Route::post('/pilih-kebun', [AuthController::class, 'selectKebun']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/{kebun_id}', [DashboardController::class, 'show'])->name('dashboard.show');

// Group rute untuk Mandor Panen
Route::prefix('mandor-panen')->name('mandor_panen.')->group(function () {
    Route::get('/', [MandorPanenController::class, 'index'])->name('index');
    Route::post('/', [MandorPanenController::class, 'store'])->name('store');
    Route::put('/{id}', [MandorPanenController::class, 'update'])->name('update');
    Route::delete('/{id}', [MandorPanenController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/edit', [MandorPanenController::class, 'edit'])->name('edit');
});

// Group rute untuk Referensi
Route::prefix('referensi')->name('referensi.')->group(function () {
    Route::get('/mandor-panen', [MandorPanenController::class, 'index'])->name('mandor_panen');
    Route::get('/mandor-karyawan', [MandorKaryawanController::class, 'index'])->name('mandor_karyawan.index');
    Route::post('/mandor-karyawan/store', [MandorKaryawanController::class, 'store'])->name('mandor_karyawan.store');

    // Rute AJAX untuk mendapatkan data berdasarkan Afdeling
    Route::get('/get-mandor/{kd_afd}', [MandorKaryawanController::class, 'getMandorByAfdeling'])->name('get_mandor');
    Route::get('/get-plant/{kd_afd}', [MandorKaryawanController::class, 'getPlantByAfdeling'])->name('get_plant');
    Route::get('/get-karyawan', [MandorKaryawanController::class, 'getKaryawanByMandor'])->name('get_karyawan');

    Route::get('/get-afdeling', [MandorKaryawanController::class, 'getAfdeling'])->name('get_afdeling');
    Route::get('/get-dik', [MandorKaryawanController::class, 'getDik'])->name('get_dik');
    Route::get('/get-data', [MandorKaryawanController::class, 'getData'])->name('get_data');
});

// Group rute untuk Mandor Karyawan
Route::prefix('mandor-karyawan')->name('mandor_karyawan.')->group(function () {
    Route::get('/', [MandorKaryawanController::class, 'index'])->name('index');
    Route::post('/store', [MandorKaryawanController::class, 'store'])->name('store');
    Route::get('/get-mandor/{kd_afd}', [MandorKaryawanController::class, 'getMandorByAfdeling'])->name('get_mandor');
    Route::post('/get-karyawan', [MandorKaryawanController::class, 'getKaryawanByMandor'])->name('get_karyawan');
});

Route::get('/referensi/mandor_karyawan_input', [MandorKaryawanController::class, 'index'])
    ->name('referensi.mandor_karyawan_input.index');
Route::post('/referensi/get-karyawan', [MandorKaryawanController::class, 'getKaryawanByMandor']);
Route::get('/referensi/get-karyawan', [MandorKaryawanController::class, 'getKaryawanByMandor']);
Route::post('/referensi/tambah-karyawan', [MandorKaryawanController::class, 'tambahKaryawan']);
Route::post('/referensi/hapus-karyawan', [MandorKaryawanController::class, 'hapusKaryawan']);
Route::get('/referensi/get-karyawan-tanpa-mandor/{id}', [MandorKaryawanController::class, 'getKaryawanTanpaMandor']);

// Halaman tes
Route::get('/test', function () {
    return view('tes');
});
