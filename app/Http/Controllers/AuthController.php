<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;
        $password = $request->password;

        $query = DB::connection("AMCO")->table("users")->where("username", $username);
        $count = $query->count();
        $data = $query->first();

        if ($count == 0) {
            return back()->withErrors(['username' => 'Username tidak terdaftar.']);
        }

        if (Hash::check($password, $data->password)) {
            // Simpan user ke session (jika diperlukan manual login)
            session(['user_logged_in' => $data]);
            return redirect()->route('pilih.kebun');
        }

        return back()->withErrors(['username' => 'Password salah.']);
    }

    public function showKebunSelection()
    {
        $kebunList = DB::connection('MASTERREF')->table('Ref_Kebun')
            ->select('KodeKebun', 'NamaKebun')
            ->get();

        return view('auth.pilih_kebun', compact('kebunList'));
    }

    public function selectKebun(Request $request)
    {
        $request->validate([
            'kebun_id' => 'required'
        ]);

        $exists = DB::connection('MASTERREF')->table('Ref_Kebun')
            ->where('KodeKebun', $request->kebun_id)
            ->exists();

        if (!$exists) {
            return back()->withErrors(['kebun_id' => 'Kebun tidak ditemukan.']);
        }

        session(['selected_kebun' => $request->kebun_id]);

        return redirect()->route('dashboard');
    }

    // ✅ Ganti dari "showLoginForm" ke:
    public function showDashboard()
    {
        $kebun_id = session('selected_kebun');

        if (!$kebun_id) {
            return redirect()->route('pilih.kebun')->withErrors('Silakan pilih kebun terlebih dahulu.');
        }

        $kebun = DB::connection("MASTERREF")->table("Ref_Kebun")
            ->where("KodeKebun", $kebun_id)->first();

        if (!$kebun) {
            return redirect()->route('pilih.kebun')->withErrors('Kebun yang dipilih tidak ditemukan.');
        }

        return view('dashboard.index', compact('kebun'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function updatePassword()
    {
        $user = User::find(1);
        if ($user) {
            $user->password = Hash::make('password_baru');
            $user->save();
            return "Password berhasil diperbarui!";
        }
        return "User tidak ditemukan!";
    }

    public function showLoginForm()
{
    return view('auth.login');
}

}
