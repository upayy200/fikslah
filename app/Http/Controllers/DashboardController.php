<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kebun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Pastikan hanya user yang sudah login bisa mengakses dashboard
        $this->middleware('auth');
    }

    public function index()
    {
        // Ambil ID kebun yang dipilih dari session
        $kebun_id = session('selected_kebun');

        // Jika tidak ada kebun yang dipilih, arahkan ke halaman pemilihan kebun
        if (!$kebun_id) {
            return redirect()->route('pilih.kebun')->withErrors('Silakan pilih kebun terlebih dahulu.');
        }

        // Cari data kebun berdasarkan ID
        $kebun = Kebun::find($kebun_id);

        // Jika kebun tidak ditemukan, kembali ke pemilihan kebun
        if (!$kebun) {
            return redirect()->route('pilih.kebun')->withErrors('Kebun yang dipilih tidak ditemukan.');
        }

        // Kirim data kebun ke view
        return view('dashboard.index', compact('kebun'));
    }

    public function show($kebun_id)
    {
        // Cari kebun berdasarkan ID, jika tidak ditemukan akan otomatis menampilkan 404
        $kebun = Kebun::findOrFail($kebun_id);

        // Kirim data kebun ke view dashboard.index
        return view('dashboard.index', compact('kebun'));
    }

    public function showKebunSelection()
    {
        return view('auth.pilih_kebun'); // Pastikan ada view 'pilih_kebun.blade.php'
    }

    public function pilihKebun(Request $request)
    {
        // Validasi request
        $request->validate([
            'kebun_id' => 'required|exists:kebun,id'
        ]);

        // Simpan kebun yang dipilih ke dalam session
        session(['selected_kebun' => $request->kebun_id]);

        // Redirect ke dashboard
        return redirect()->route('dashboard.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Anda telah logout.');
    }
}
