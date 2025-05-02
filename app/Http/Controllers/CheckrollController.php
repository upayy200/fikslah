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
    DB::connection('checkroll')->beginTransaction();

    try {
        $validated = $request->validate([
            'tanggal' => 'required|date_format:d-m-Y',
            'kd_afd' => 'required',
            'reg_mandor' => 'required',
            'data' => 'required|min:1'
        ]);

        $dataKaryawan = json_decode($request->data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Format data karyawan tidak valid');
        }

        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $kodeafd = str_replace('AFD', '', $request->kd_afd);
        $kodeunit = session('selected_kebun');

        $savedCount = 0;
        $errors = [];

        foreach ($dataKaryawan as $item) {
            try {
                // Skip jika tidak ada absen (karena ini field utama)
                if (empty($item['absen'])) continue;

                $karyawan = DB::connection('AMCO')
                    ->table('AMCO_Dik as d')
                    ->join('AMCO_MandorKaryawan as mk', 'd.REG', '=', 'mk.Register')
                    ->where('d.REG', $item['reg'])
                    ->first();

                if (!$karyawan) {
                    $errors[] = "Karyawan dengan REG {$item['reg']} tidak ditemukan";
                    continue;
                }

                $dataToSave = [
                    'tanggal' => $tanggal,
                    'SAP_TargetAlokasi' => $item['SAP_TargetAlokasi'] ?? null,
                    'kodeunit' => $karyawan->KD_KBN ?? $kodeunit,
                    'kodeafd' => $karyawan->KD_AFD ?? $kodeafd,
                    'regmdr' => $karyawan->regmdr ?? $request->reg_mandor,
                    'register' => $karyawan->REG,
                    'kodeabs' => $item['absen'],
                    'nama' => $karyawan->NAMA,
                    'TglInput' => now(),
                ];

                // Tambahkan field opsional hanya jika ada nilai
                $optionalFields = [
                    'target_alokasi', 'kdblok', 'thntnm', 'jelajahHA', 'satuan',
                    'hslpanen', 'jmlkg', 'stpikul', 'pct', 'ms', 'jendangan'
                ];

                foreach ($optionalFields as $field) {
                    if (isset($item[$field]) && $item[$field] !== '') {
                        $dataToSave[$field] = $item[$field];
                    }
                }

                // Cek apakah data sudah ada
                $existing = DB::connection('checkroll')
                    ->table('GajiAbsensi')
                    ->where('tanggal', $tanggal)
                    ->where('register', $karyawan->REG)
                    ->first();

                if ($existing) {
                    // Update hanya jika ada perubahan
                    DB::connection('checkroll')
                        ->table('GajiAbsensi')
                        ->where('register', $existing->register)
                        ->where('tanggal', $tanggal)
                        ->update($dataToSave);
                } else {
                    // Insert baru
                    DB::connection('checkroll')
                        ->table('GajiAbsensi')
                        ->insert($dataToSave);
                }

                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = "Gagal simpan data {$item['reg']}: " . $e->getMessage();
                Log::error("Gagal simpan absen {$item['reg']}: " . $e->getMessage());
            }
        }

        DB::connection('checkroll')->commit();

        return response()->json([
            'success' => $savedCount > 0,
            'message' => "Data berhasil disimpan ($savedCount record)" . (!empty($errors) ? " | Error: " . implode(', ', $errors) : ''),
            'errors' => $errors
        ]);

    } catch (\Exception $e) {
        DB::connection('checkroll')->rollBack();
        Log::error('Error utama simpan absensi: ' . $e->getMessage());
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
    
    return DB::connection('AMCO')
        ->table('AMCO_BlokSAP')
        ->select('Blok_SAP', 'NamaBlok', 'Uraian', 'ThnTnm')
        ->where('KodeUnit', $selectedKebun)
        ->where('KomoditiCode', 'TH') // Hanya untuk komoditi Teh
        ->orderBy('NamaBlok')
        ->get();
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


}
