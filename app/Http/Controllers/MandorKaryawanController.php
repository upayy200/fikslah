<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MandorKaryawanController extends Controller
{
    // 🔹 Menampilkan form dengan daftar Afdeling
    public function index()
    {
        // Ambil kebun yang dipilih dari session
        $kodeKebun = session('selected_kebun');
    
        // Cek jika tidak ada kebun yang dipilih
        if (!$kodeKebun) {
            return redirect()->route('pilih.kebun')->withErrors('Silakan pilih kebun terlebih dahulu.');
        }
    
        // Ambil afdeling berdasarkan kebun yang dipilih
        $afdeling = DB::connection('AMCO')->table('AMCO_Afdeling')
            ->where('KodeKebun', $kodeKebun)
            ->select('KodeAfdeling as id', 'NamaAfdeling as nama')
            ->get();
    
        // Ambil kode afdeling pertama sebagai plant
        $plant = DB::connection('AMCO')->table('AMCO_Afdeling')
    ->where('KodeKebun', $kodeKebun)
    ->value('Plant') ?? 'Tidak Diketahui'; 

    
        return view('referensi.mandor_karyawan_input', compact('afdeling', 'plant'));
    }
    


    // 🔹 Mengambil daftar afdeling (Dari DropdownController)
    public function getAfdeling()
    {
        $afdeling = DB::connection('AMCO')->table('AMCO_Afdeling')
            ->select('KodeKebun', 'NamaAfdeling')
            ->get();
        
        return response()->json($afdeling);
    }

    // 🔹 Mengambil daftar Reg. Mandor berdasarkan Kd. Afd (Dari DropdownController)
    public function getDik(Request $request)
    {
        $kdAfd = $request->kd_afdeling;

        $dik = DB::connection('AMCO')->table('AMCO_Dik')
            ->where('KD_AFD', $kdAfd)
            ->select('REG', 'NAMA')
            ->get();

        return response()->json($dik);
    }

    // 🔹 Mengambil daftar karyawan berdasarkan Reg. Mandor yang dipilih (Dari DropdownController)
    public function getData(Request $request)
    {
        $regMandor = $request->reg_mandor;

        if (!$regMandor) {
            return response()->json(['error' => 'Pilih Reg. Mandor terlebih dahulu'], 400);
        }

        $data = DB::connection('AMCO')->table('AMCO_MandorKaryawan')
            ->where('Regmdr', $regMandor)
            ->select('Register', 'Nama')
            ->get();

        return response()->json($data);
    }

    // 🔹 Mengambil daftar mandor berdasarkan Kode Afdeling
    public function getMandorByAfdeling($kd_afd)
    {
        try {
            $mandor = DB::connection('AMCO')
                ->table('AMCO_MandorKaryawan')
                ->join('AMCO_Dik', 'AMCO_MandorKaryawan.Regmdr', '=', 'AMCO_Dik.REG')
                ->where('AMCO_MandorKaryawan.KodeAfdeling', $kd_afd)
                ->select('AMCO_MandorKaryawan.Regmdr', 'AMCO_Dik.NAMA as NamaMandor')
                ->distinct()
                ->get();

            return response()->json($mandor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data mandor'], 500);
        }
        
    }

    // 🔹 Menyimpan data inputan ke database
    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m-d',
            'kd_afd' => 'required|exists:AMCO_Afdeling,KodeAfdeling',
            'plant' => 'required|string',
            'reg_mandor' => 'required|exists:AMCO_Dik,REG',
        ]);

        try {
            DB::connection('AMCO')->table('AMCO_MandorKaryawan')->insert([
                'Tanggal' => $request->bulan,
                'KdAfd' => $request->kd_afd,
                'KodeUnit' => $request->plant,
                'Regmdr' => $request->reg_mandor,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // 🔹 Mengambil Plant berdasarkan Kode Afdeling
    public function getPlantByAfdeling($kd_afd)
    {
        try {
            $plant = DB::connection('AMCO')->table('AMCO_Afdeling')
                ->where('KdAfd', $kd_afd)
                ->first(['Plant']);

            return response()->json($plant);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data plant'], 500);
        }
    }

    // 🔹 Mengambil data karyawan berdasarkan tanggal, afdeling, dan mandor
    public function getKaryawanByMandor(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'kd_afd' => 'required|exists:AMCO_Afdeling,KodeAfdeling',
        'reg_mandor' => 'required|exists:AMCO_MandorKaryawan,Regmdr',
    ]);

    try {
        $karyawan = DB::connection('AMCO')->table('AMCO_MandorKaryawan as mk')
            ->join('AMCO_Dik as dik', 'mk.Register', '=', 'dik.REG')
            ->selectRaw('
                mk.Register, 
                mk.RegSAP, 
                mk.Nama, 
                mk.sts, 
                dik.NAMA_JAB
            ')
            ->whereDate('mk.Tanggal', '=', $request->tanggal)
            ->where('mk.KodeAfdeling', '=', $request->kd_afd)
            ->where('mk.Regmdr', '=', $request->reg_mandor)
            ->groupBy('mk.Register', 'mk.RegSAP', 'mk.Nama', 'mk.sts', 'dik.NAMA_JAB')
            ->distinct() // Menghilangkan duplikasi
            ->get();

        return response()->json($karyawan);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal mengambil data karyawan: ' . $e->getMessage()], 500);
    }
}
public function tambahKaryawan(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'kd_afd' => 'required|exists:AMCO_Afdeling,KodeAfdeling',
        'reg_mandor' => 'required|exists:AMCO_MandorKaryawan,Regmdr',
        'register' => 'required|exists:AMCO_Dik,REG'
    ]);

    try {
        DB::connection('AMCO')->beginTransaction();

        // Cek apakah karyawan sudah memiliki mandor sebelumnya
        $mandorLama = DB::connection('AMCO')->table('AMCO_MandorKaryawan')
            ->where('Register', $request->register)
            ->first();

        if ($mandorLama) {
            // Jika ada, hapus dari mandor lama
            DB::connection('AMCO')->table('AMCO_MandorKaryawan')
                ->where('Register', $request->register)
                ->delete();
        }

        // Tambahkan karyawan ke mandor baru
        DB::connection('AMCO')->table('AMCO_MandorKaryawan')->insert([
            'Tanggal' => $request->tanggal,
            'KodeAfdeling' => $request->kd_afd,
            'Regmdr' => $request->reg_mandor,
            'Register' => $request->register,
            'RegSAP' => DB::connection('AMCO')->table('AMCO_Dik')->where('REG', $request->register)->value('REG_SAP'),
            'Nama' => DB::connection('AMCO')->table('AMCO_Dik')->where('REG', $request->register)->value('Nama'),
            'sts' => $request->sts,
        ]);

        DB::connection('AMCO')->commit();

        return response()->json(['success' => 'Karyawan berhasil dipindahkan ke mandor baru']);
    } catch (\Exception $e) {
        DB::connection('AMCO')->rollBack();
        return response()->json(['error' => 'Gagal memindahkan karyawan: ' . $e->getMessage()], 500);
    }
}

public function hapusKaryawan(Request $request)
{
    $request->validate([
        'register' => 'required|exists:AMCO_MandorKaryawan,Register'
    ]);

    try {
        DB::connection('AMCO')->table('AMCO_MandorKaryawan')
            ->where('Register', $request->register)
            ->delete();

        return response()->json(['success' => 'Karyawan berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghapus karyawan: ' . $e->getMessage()], 500);
    }
}
public function getKaryawanTanpaMandor($Regmdr)
{
    $karyawan = DB::connection('AMCO')->table('AMCO_MandorKaryawan as mk')
        ->leftJoin('AMCO_Dik as dik', 'mk.RegSAP', '=', 'dik.REG_SAP')
        ->where(function ($query) use ($Regmdr) {
            $query->whereNull('mk.Regmdr')
                  ->orWhere('mk.Regmdr', '!=', $Regmdr);
        })
        ->select(
            'mk.Register', 
            'mk.RegSAP', 
            'mk.sts', 
            'mk.Nama', 
            'dik.NAMA_JAB'
        )
        ->distinct()
        ->get();

    return response()->json($karyawan);
}


}



