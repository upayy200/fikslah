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
        $kodeKebun = session('selected_kebun');
    
        if (!$kodeKebun) {
            return response()->json(['data' => [], 'error' => 'Kebun belum dipilih'], 400);
        }
    
        // Ambil data branded dari MASTERREF sesuai kebun
        $branded = DB::connection('checkroll')
            ->table(DB::raw('[MASTERREF].[dbo].[Ref_Branded]'))
            ->where('kodeunit', $kodeKebun)
            ->get();
    
        // Ambil data rijek sesuai tanggal
        $dataRijek = DB::connection('checkroll')->table('GajiRijek_2025')
            ->where('Tanggal', $tanggal)
            ->get()
            ->keyBy('Kdbrand');
    
        // Gabungkan data
        $result = $branded->map(function ($item) use ($dataRijek) {
            $rijek = $dataRijek[$item->kdbranded] ?? null;
    
            return [
                'KodeBranded' => $item->kdbranded,
                'NamaBranded' => $item->nmbranded,
                'Rijek'       => $rijek->Rijek ?? 0,
                'NomorBA'     => $rijek->No_BA ?? '',
            ];
        });
    
        return response()->json(['data' => $result]);
    }
    
    


    public function update(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $data = $request->input('data');

        foreach ($data as $item) {
            DB::connection('checkroll')->table('GajiRijek_2025')->updateOrInsert(
                [
                    'Tanggal' => $tanggal,
                'Kdbrand' => $item['KodeBranded'],
                ],
                [
                    'Rijek' => $item['Rijek'],
                'No_BA' => $item['NomorBA'],
                ]
            );
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    }
}
