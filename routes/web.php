<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/pilih-kebun', [AuthController::class, 'showKebunSelection'])->name('pilih.kebun');
    Route::post('/pilih-kebun', [AuthController::class, 'selectKebun']);
    
    Route::get('/dashboard', function () {
        $kebun_id = session('selected_kebun');
        return view('dashboard', compact('kebun_id'));
    })->name('dashboard');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{kebun_id}', [DashboardController::class, 'show'])->name('dashboard.show');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');