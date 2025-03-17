<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MandorPanenController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\MandorKaryawanController;

// Halaman utama redirect ke login
Route::get('/', function () {
    return view('auth.login');
});

// Rute untuk login dan logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute setelah login
Route::get('/pilih-kebun', [AuthController::class, 'showKebunSelection'])->name('pilih.kebun');
Route::post('/pilih-kebun', [AuthController::class, 'selectKebun']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('referensi.mandor_karyawan_input');
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
    Route::get('/mandor_panen', [MandorPanenController::class, 'index'])->name('mandor_panen');
    Route::get('/mandor_karyawan_input', [MandorKaryawanController::class, 'index'])->name('mandor_karyawan_input.index');
    Route::post('/mandor_karyawan_input/store', [MandorKaryawanController::class, 'store'])->name('mandor_karyawan_input.store');

    // Rute AJAX untuk mendapatkan data Mandor dan Plant berdasarkan Kd.Afd
    Route::get('/get-mandor/{kd_afd}', [MandorKaryawanController::class, 'getMandorByAfdeling'])->name('get_mandor');
    Route::get('/get-plant/{kd_afd}', [MandorKaryawanController::class, 'getPlantByAfdeling'])->name('get_plant');
});

// Rute untuk AJAX dropdown lainnya
Route::get('/get-afdeling', [DropdownController::class, 'getAfdeling'])->name('get.afdeling');
Route::get('/get-dik', [DropdownController::class, 'getDik'])->name('get.dik');
Route::get('/get-data', [DropdownController::class, 'getData'])->name('get.data');

Route::post('/mandor-karyawan/get-data', [MandorKaryawanController::class, 'getData'])
    ->name('referensi.mandor_karyawan_input.getData');

route::get('/test', function () {
    return view('tes');
});
