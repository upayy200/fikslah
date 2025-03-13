<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MandorPanen;

class MandorPanenController extends Controller
{
    public function index()
    {
        $mandorPanen = MandorPanen::all(); // Perbaiki penulisan nama kelas model
        return view('referensi.mandor_panen', compact('mandorPanen'));
    }
    public function store(Request $request)
{
    // Validasi data
    $request->validate([
        'bulan' => 'required|date',
        'kd_afd_bagian' => 'required|string',
        'plant' => 'required|string',
        'reg_mb' => 'required|string',
        'regmdr' => 'required|string',
        'regmdr_sap' => 'required|string',
        'status' => 'required|string',
        'nama' => 'required|string',
        'jabatan' => 'required|string',
    ]);

    // Simpan ke database
    MandorPanen::create($request->all());

    return redirect()->route('mandor_panen.index')->with('success', 'Data berhasil ditambahkan');
}

    public function update(Request $request, $id)
    {
        $mandor = MandorPanen::findOrFail($id); // Perbaiki penulisan nama kelas model
        $mandor->update($request->all());
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        MandorPanen::findOrFail($id)->delete(); // Perbaiki penulisan nama kelas model
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}