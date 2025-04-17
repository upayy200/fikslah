<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CheckrollController extends Controller
{
    public function komoditiTeh()
    {
        $selectedKebun = session('selected_kebun');

        $afdBagian = DB::connection('AMCO')
            ->table('AMCO_Afdeling')
            ->select('KodeAfdeling', 'NamaAfdeling')
            ->where('KodeKebun', $selectedKebun)
            ->get();

        return view('checkroll.komoditi_teh', compact('afdBagian'));
    }

    public function get_target_alokasi()
    {
        $data = DB::TABLE("AMCO_TargetAlokasiBiaya")->get();

        return response()->json(['data' => $data]);
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
            'data' => 'required|array'
        ]);

        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $kodeafd = str_replace('AFD', '', $request->kd_afd);
        $kodeunit = session('selected_kebun');

        $savedCount = 0;
        $errors = [];

        foreach ($request->data as $item) {
            if (empty($item['absen'])) continue;

            try {
                $karyawan = DB::connection('AMCO')
                    ->table('AMCO_Dik as d')
                    ->join('AMCO_MandorKaryawan as mk', 'd.REG', '=', 'mk.Register')
                    ->where('d.REG', $item['reg'])
                    ->first();

                if (!$karyawan) {
                    $errors[] = "Data karyawan tidak ditemukan: {$item['reg']}";
                    continue;
                }

                $dataToSave = [
                    'tanggal' => $tanggal,
                    'kodeunit' => $karyawan->KD_KBN ?? $kodeunit,
                    'kodeafd' => $karyawan->KD_AFD ?? $kodeafd,
                    'regmdr' => $karyawan->regmdr ?? $request->reg_mandor,
                    'register' => $karyawan->REG,
                    'kodeabs' => $item['absen'],
                    'nama' => $karyawan->NAMA,
                    'TglInput' => now(),
                ];

                // Cek existing data
                $existing = DB::connection('checkroll')
                    ->table('GajiAbsensi')
                    ->where('tanggal', $tanggal)
                    ->where('register', $item['reg'])
                    ->first();

                if ($existing) {
                    DB::connection('checkroll')
                        ->table('GajiAbsensi')
                        ->where('register', $existing->register)
                        ->update($dataToSave);
                } else {
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

        $message = "Data berhasil disimpan ($savedCount record)";
        if (!empty($errors)) {
            $message .= " | Error: " . implode(', ', $errors);
        }

        return response()->json([
            'success' => $savedCount > 0,
            'message' => $message,
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


}