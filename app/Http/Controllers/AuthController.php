<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kebun;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{


    public function showLoginForm()
    {
        // Ambil ID kebun yang dipilih dari session
        $kebun_id = session('selected_kebun');

        // Jika tidak ada kebun yang dipilih, arahkan ke halaman pemilihan kebun
        if (!$kebun_id) {
            return redirect()->route('pilih.kebun')->withErrors('Silakan pilih kebun terlebih dahulu.');
        }

        // Cari data kebun berdasarkan ID
        //$kebun = Kebun::find($kebun_id);

        $kebun = DB::CONNECTION("MASTERREF")->TABLE("Ref_Kebun")->Where("KodeKebun", $kebun_id)->first();

        // Jika kebun tidak ditemukan, kembali ke pemilihan kebun
        if (!$kebun) {
            return redirect()->route('pilih.kebun')->withErrors('Kebun yang dipilih tidak ditemukan.');
        }

        // Kirim data kebun ke view
        return view('dashboard.index', compact('kebun'));
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        /*if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('pilih.kebun');
        }*/

        $username = $request->username;
        $password = $request->password;
        $encryptedPassword = Hash::make($password);

        $query = DB::CONNECTION("AMCO")->TABLE("users")->Where("username", $username);
        $count = $query->count();
        $data = $query->first();

        if ($count == 0) {
            return back()->withErrors(['username' => 'Username tidak terdaftar.']);
        }

        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            return redirect()->route('pilih.kebun');
        }
        

        return back()->withErrors(['username' => 'Password salah.']);
    }




    public function showKebunSelection()
    {
        // Ambil data dari database MASTERREF tanpa model
        $kebunList = DB::connection('MASTERREF')->table('Ref_Kebun')
            ->select('KodeKebun', 'NamaKebun')
            ->where('Status', 1) 
            ->get();

        return view('auth.pilih_kebun', compact('kebunList'));
    }




    public function selectKebun(Request $request)
    {
        $request->validate([
            'kebun_id' => 'required'
        ]);

        // Pastikan kebun yang dipilih ada di database MASTERREF
        $exists = DB::connection('MASTERREF')->table('Ref_Kebun')
            ->where('KodeKebun', $request->kebun_id)
            ->exists();

        if (!$exists) {
            return back()->withErrors(['kebun_id' => 'Kebun tidak ditemukan.']);
        }

        session(['selected_kebun' => $request->kebun_id]);
        session()->put('selected_kebun', $request->kebun_id);


        return redirect('/dashboard');
    }



    public function updatePassword()
    {
        $user = User::find(1); // Ganti dengan ID user
        if ($user) {
            $user->password = Hash::make('password_baru'); // Ganti password
            $user->save();
            return "Password berhasil diperbarui!";
        }
        return "User tidak ditemukan!";
    }





    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); // Logout pengguna

        $request->session()->invalidate(); // Hapus sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF

        return redirect('/login'); // Redirect ke halaman login
    }
}