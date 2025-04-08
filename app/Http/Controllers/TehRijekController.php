<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TehRijekController extends Controller
{
    public function index()
    {
        return view('referensi.teh_rijek');

    }

    public function load(Request $request)
{
    $tanggal = $request->input('tanggal');

    // Cek apakah user login, jika tidak beri fallback default atau error aman
    $user = auth()->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $kodeunit = $user->kode_kebun;

    $data = DB::connection('checkroll') // Ganti sesuai nama baru database
        ->table('GajiRijek_2025 as g')
        ->join('MASTERREF.dbo.Ref_Branded as r', 'g.Kdbrand', '=', 'r.kdbranded')
        ->select('g.Kdbrand', 'r.nmbranded', 'g.Rijek', 'g.No_BA')
        ->whereDate('g.Tanggal', $tanggal)
        ->where('g.Kodeunit', $kodeunit)
        ->get();

    return response()->json($data);
}

    public function update(Request $request)
    {
        $kodeunit = auth()->user()->kode_kebun;

        DB::connection('checkroll')
            ->table('GajiRijek_2025')
            ->where('Kdbrand', $request->Kdbrand)
            ->where('Tanggal', $request->Tanggal)
            ->where('Kodeunit', $kodeunit)
            ->update(['Rijek' => $request->Rijek]);

        return response()->json(['success' => 'Data berhasil diperbarui']);
    }
}
