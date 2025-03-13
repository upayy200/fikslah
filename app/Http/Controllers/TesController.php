<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TesController extends Controller
{
    public function index()
{
    // Ambil data dari tabel 'AMCO_Dik' dan 'AMCO_Afdeling'
    $dataDik = DB::connection('sqlsrv')->table('AMCO_Dik as dik')
        ->select('dik.REG', 'dik.REG_SAP', 'dik.NAMA', 'dik.NAMA_JAB', 'dik.KD_AFD', 'dik.KD_JAB')
        ->get();

    $dataAfdeling = DB::connection('sqlsrv')->table('AMCO_Afdeling')->get();

    // Kirim data ke view
    return view('referensi.mandor_karyawan_input', compact('dataDik', 'dataAfdeling'));
}
public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'bulan' => 'required|date',
        'kd_afd' => 'required',
        'plant' => 'required|string',
        'reg_mandor' => 'required|string',
    ]);

    // Simpan ke database
    DB::connection('sqlsrv')->table('AMCO_Dik')->insert([
        'REG' => strtoupper($request->reg_mandor),
        'NAMA' => 'Nama Default', // Ubah sesuai kebutuhan
        'KD_AFD' => $request->kd_afd,
        'KD_JAB' => 'Jabatan Default', // Ubah sesuai kebutuhan
        'REG_SAP' => 'SAP12345', // Ubah sesuai kebutuhan
        'bulan' => $request->bulan,
        'plant' => $request->plant,
    ]);

    return redirect()->route('referensi.mandor_karyawan_input.index')->with('success', 'Data berhasil disimpan!');
}
}

