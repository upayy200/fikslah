<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CheckrollController extends Controller
{
    public function komoditiTeh(Request $request)
{
    $selectedKebun = session('selected_kebun');

    // Pastikan selalu return array, bahkan ketika kosong
    $afdBagian = DB::connection('AMCO')
        ->table('AMCO_Afdeling')
        ->select('KodeAfdeling', 'NamaAfdeling')
        ->where('KodeKebun', $selectedKebun)
        ->get() ?? [];

    // Inisialisasi dengan array kosong
    $karyawan = [];
    $absen = [];
    $target_alokasi = [];

    if ($request->has(['reg', 'kd'])) {
        $reg = $request->input('reg');
        $kd = str_replace('AFD', '', $request->input('kd'));

        $dataExists = DB::connection("checkroll")
            ->table("GajiAbsensi")
            ->where("regmdr", $reg)
            ->where("kodeafd", $kd)
            ->exists();

        if ($dataExists) {
            $karyawan = DB::connection('AMCO')
                ->table('AMCO_MandorKaryawan as mk')
                ->join('AMCO_Dik as d', 'mk.Register', '=', 'd.REG')
                ->where('mk.regmdr', $reg)
                ->where('mk.KodeAfdeling', $request->kd)
                ->select([
                    'd.REG',
                    'd.REG_SAP',
                    'd.NAMA',
                    'd.NAMA_JAB',
                    'd.KD_AFD',
                    
                ])
                ->distinct()
                ->get() ?? [];

            $absen = DB::Connection("AMCO")
                ->table("AMCO_KodeAbsen")
                ->select('KodeAbsen', 'Uraian')
                ->get() ?? [];

            $target_alokasi = DB::connection('AMCO')
            ->table('AMCO_TargetAlokasiBiaya')
            ->select('TargetAlokasi', 'Uraian')
            ->get();

            if ($target_alokasi->isEmpty()) {
                logger()->error('Data Target Alokasi kosong');
            } else {
                logger()->info('Data Target Alokasi:', $target_alokasi->toArray());
            }
        
            session()->put([
                'target_alokasi' => $target_alokasi,
                'absen' => $absen,
                'karyawan' => $karyawan,
                'reg' => $reg,
                'kd' => $kd
            ]);
        }
    }

    return view('checkroll.komoditi_teh', [
        'afdBagian' => $afdBagian,
        'karyawan' => $karyawan,
        'absen' => $absen,
        'target_alokasi' => $target_alokasi,
        'error' => session('error'),
        'success' => session('success')
    ]);
}



    
    
    public function getMandorByAfd(Request $request)
    {
        $kdAfd = $request->kd_afd;

        $mandor = DB::connection('AMCO')
            ->table('AMCO_Dik AS d')
            ->join('AMCO_MandorKaryawan AS mk', 'mk.regmdr', '=', 'd.REG')
            ->where('mk.KodeAfdeling', $kdAfd)
            ->where('d.KD_KBN', session('selected_kebun'))
            ->select('d.REG', 'd.NAMA')
            ->distinct()
            ->get();

        return response()->json($mandor);
    }

    public function getKaryawanByMandor(Request $request)
    {
        $regMandor = $request->input('reg_mandor');
        $kodeafd = $request->input('kd_afd');
        $tanggal = Carbon::createFromFormat('d-m-Y', $request->input('tanggal'))->format('Y-m-d');
    
        try {
            // Ambil data karyawan
            $karyawan = DB::connection('AMCO')
                ->table('AMCO_MandorKaryawan as mk')
                ->join('AMCO_Dik as d', 'mk.Register', '=', 'd.REG')
                ->where('mk.regmdr', $regMandor)
                ->where('mk.KodeAfdeling', $kodeafd)
                ->select([
                    'd.REG',
                    'd.REG_SAP',
                    'd.NAMA',
                    'd.NAMA_JAB',
                    'd.KD_AFD'
                ])
                ->distinct()
                ->get();
    
            // Ambil data absen yang sudah ada (jika ada)
            $absensi = DB::connection('checkroll')
                ->table('GajiAbsensi')
                ->where('tanggal', $tanggal)
                ->where('kodeafd', str_replace('AFD', '', $kodeafd))
                ->where('regmdr', $regMandor)
                ->get()
                ->keyBy('register'); // Index by register karyawan
    
            // Gabungkan data
            $data = $karyawan->map(function ($item) use ($absensi) {
                $absen = $absensi->get($item->REG);
                return (object) array_merge((array) $item, [
                    'kodeabs' => $absen->kodeabs ?? null,
                    'pekerjaan' => $absen->pekerjaan ?? null,
                    'lokasi' => $absen->LocationCode ?? null,
                    // Tambahkan field lainnya sesuai kebutuhan
                ]);
            });
    
            return response()->json(['success' => true, 'data' => $data]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    

    public function simpanAbsensi(Request $request)
{
    DB::connection('AMCO')->beginTransaction();

    try {
        $dataKaryawan = json_decode($request->data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Format data tidak valid.');
        }

        foreach ($dataKaryawan as $data) {
            DB::connection('AMCO')
                ->table('AMCO_KKerja_BKM')
                ->insert([
                    'register' => $data['register'],
                    'mandor' => $data['mandor'],
                    'Referensi' => $data['Referensi'],
                    'Keterangan' => $data['Keterangan'],
                    'Afdeling' => $data['Afdeling'],
                    'Kehadiran' => $data['Kehadiran'],
                    'TargetAokasiBiaya' => $data['TargetAokasiBiaya'],
                    'LocationCode' => $data['LocationCode'],
                    'thntnm' => $data['thntnm'],
                    'Aktifitas' => $data['Aktifitas'],
                    'Luasan' => $data['Luasan'],
                    'kgpikul' => $data['kgpikul'],
                    'stpikul' => $data['stpikul'],
                    'grup' => $data['grup'],
                    'Jendangan' => $data['Jendangan'],
                    'Tanggal' => Carbon::createFromFormat('d-m-Y', $data['Tanggal'])->format('Y-m-d'),
                    'plant' => $data['plant']
                ]);
        }

        DB::connection('AMCO')->commit();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.'
        ]);

    } catch (\Exception $e) {
        DB::connection('AMCO')->rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ], 500);
    }
}

public function getCostCenter()
{
    $selectedKebun = session('selected_kebun');
    
    if (!$selectedKebun) {
        return response()->json(['error' => 'Kebun tidak terdeteksi'], 400);
    }

    try {
        $costCenters = DB::connection('AMCO')
            ->table('AMCO_CostCenter')
            ->select('CostCenter', 'Uraian')
            ->where('KodeUnit', $selectedKebun)
            ->where('stat', '1')
            ->orderBy('CostCenter')
            ->get();

        return response()->json($costCenters);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal mengambil data Cost Center',
            'message' => $e->getMessage()
        ], 500);
    }
}
public function getBlokSAP(Request $request)
{
    $selectedKebun = session('selected_kebun');
    
    $bloks = DB::connection('AMCO')
        ->table('AMCO_BlokSAP')
        ->select('Blok_SAP','NamaBlok', 'Uraian', 'ThnTnm')
        ->where('KodeUnit', $selectedKebun)
        ->where('KomoditiCode', 'TH')
        ->orderBy('Blok_SAP')
        ->get();

    return $bloks->map(function($item) {
        return [
            'Blok_SAP' => $item->Blok_SAP,
            'NamaBlok'=>$item->NamaBlok,
            'Uraian' => $item->Uraian,
            'ThnTnm' => $item->ThnTnm
        ];
    });
}
public function getAktifitasTH()
{
    $data = DB::table('AMCO.dbo.AMCO_Aktifitas')
        ->where('KomoditiCode', 'TH')
        ->select('Aktifitas', 'Uraian')
        ->where('stat', 1)
        ->orderBy('Aktifitas')
        ->get();

    return response()->json($data);
}

public function getMesinPetik(Request $request)
{
    $selectedKebun = session('selected_kebun');
    $kodeAfd = $request->input('kode_afd');

    \Log::info('Mengambil mesin petik', [
        'kode_unit' => $selectedKebun,
        'kode_afd' => $kodeAfd
    ]);

    try {
        $mesinPetik = DB::connection('MASTERREF')
            ->table('Ref_MesinPetik')
            ->select('Dataran', 'KodeMesin', 'NamaAfd')
            ->where('KodeUnit', $selectedKebun)
            ->where('Kodeafd', $kodeAfd)
            ->orderBy('KodeMesin')
            ->get();

        \Log::info('Data ditemukan:', $mesinPetik->toArray());

        return response()->json($mesinPetik);

    } catch (\Exception $e) {
        \Log::error('Error:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Gagal mengambil data',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
