<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MandorPanenController;
use App\Http\Controllers\DropdownController;

Route::get('/', function () {
    return view('auth.login');
});

// Rute untuk login (tanpa middleware `guest`)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Middleware custom untuk memastikan user sudah login (tanpa `auth`)
Route::middleware(['checkSession'])->group(function () {
    Route::get('/pilih-kebun', [AuthController::class, 'showKebunSelection'])->name('pilih.kebun');
    Route::post('/pilih-kebun', [AuthController::class, 'selectKebun']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{kebun_id}', [DashboardController::class, 'show'])->name('dashboard.show');

    Route::prefix('mandor-panen')->name('mandor_panen.')->group(function () {
        Route::get('/', [MandorPanenController::class, 'index'])->name('index');
        Route::post('/', [MandorPanenController::class, 'store'])->name('store');
        Route::put('/{id}', [MandorPanenController::class, 'update'])->name('update');
        Route::delete('/{id}', [MandorPanenController::class, 'destroy'])->name('destroy');
    });

    Route::get('/referensi/mandor_panen', [MandorPanenController::class, 'index'])->name('referensi.mandor_panen');
    Route::get('/mandor-panen/{id}/edit', [MandorPanenController::class, 'edit'])->name('mandor_panen.edit');

    Route::prefix('referensi')->group(function () {
        Route::get('/mandor-karyawan-input', [TesController::class, 'index'])->name('referensi.mandor_karyawan_input');
    });

    Route::get('/get-afdeling', [DropdownController::class, 'getAfdeling'])->name('get.afdeling');
    Route::get('/get-dik', [DropdownController::class, 'getDik'])->name('get.dik');
    Route::get('/get-data', [DropdownController::class, 'getData'])->name('get.data');
});

// Group untuk referensi
Route::prefix('referensi')->group(function () {
    Route::get('/mandor_karyawan_input', [TesController::class, 'index'])->name('referensi.mandor_karyawan_input.index');
    Route::post('/mandor_karyawan_input', [TesController::class, 'store'])->name('referensi.mandor_karyawan_input.store');
    Route::get('/mandor_karyawan_input/{id}/edit', [TesController::class, 'edit'])->name('referensi.mandor_karyawan_input.edit');
    Route::put('/mandor_karyawan_input/{id}', [TesController::class, 'update'])->name('referensi.mandor_karyawan_input.update');
    Route::delete('/mandor_karyawan_input/{id}', [TesController::class, 'destroy'])->name('referensi.mandor_karyawan_input.destroy');
});
