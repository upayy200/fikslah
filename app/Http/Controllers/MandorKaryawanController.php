<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MandorKaryawanController extends Controller
{
    public function index()
    {
        // Gunakan koneksi 'AMCO' untuk tabel AMCO_Afdeling dan AMCO_MandorKaryawan
        $dataAfdeling = DB::connection('AMCO')->table('AMCO_Afdeling')->get();
        $dataMandorKaryawan = DB::connection('AMCO')->table('AMCO_MandorKaryawan')->paginate(50);

        return view('referensi.mandor_karyawan_input', compact('dataAfdeling', 'dataMandorKaryawan'));
    }

    // Ambil daftar mandor berdasarkan KodeAfdeling
    public function getMandorByAfdeling($kd_afd)
    {
        try {
            $mandor = DB::connection('AMCO')->table('AMCO_Dik')
                ->where('KD_AFD', $kd_afd)
                ->get(['REG', 'NAMA']);

            return response()->json($mandor);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|date',
            'kd_afd' => 'required|exists:AMCO_Afdeling,KodeAfdeling',
            'plant' => 'required|string',
            'reg_mandor' => 'required|exists:AMCO_Dik,REG',
        ]);

        DB::connection('AMCO')->table('AMCO_MandorKaryawan')->insert([
            'Tanggal' => $request->bulan,
            'KodeAfdeling' => $request->kd_afd,
            'KodeUnit' => $request->plant,
            'Regmdr' => $request->reg_mandor,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan');
    }

    public function getPlantByAfdeling($kd_afd)
    {
        try {
            $plant = DB::connection('AMCO')->table('AMCO_Afdeling')
                ->where('KodeAfdeling', $kd_afd)
                ->first(['Plant']);

            return response()->json($plant);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $request->validate([
            'kd_afd' => 'required|exists:AMCO_Afdeling,KodeAfdeling',
            'reg_mandor' => 'required|exists:AMCO_Dik,REG',
            'bulan' => 'required|date_format:Y-m',
        ]);

        list($tahun, $bulan) = explode('-', $request->bulan);

        $data = DB::TABLE('AMCO_Dik')
            ->whereYear('TANGGAL', $tahun)
            ->whereMonth('TANGGAL', $bulan)
            ->where("REG", $request->reg_mandor)
            ->get();


        return response()->json($data);
    }
}
