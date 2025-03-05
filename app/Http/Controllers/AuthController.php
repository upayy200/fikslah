<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kebun;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('pilih.kebun');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function showKebunSelection()
    {
        $kebunList = Kebun::all();
        return view('auth.pilih_kebun', compact('kebunList'));
    }

    public function selectKebun(Request $request)
    {
        $request->validate([
            'kebun_id' => 'required|exists:kebun,id'
        ]);

        session(['selected_kebun' => $request->kebun_id]);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
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

}
