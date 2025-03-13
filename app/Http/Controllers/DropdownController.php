<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropdownController extends Controller
{
    // Mengambil daftar afdeling
    public function getAfdeling()
    {
        $afdeling = DB::connection('sqlsrv')->table('amco_afdeling')->select('id', 'nama')->get();
        return response()->json($afdeling);
    }

    // Mengambil daftar Reg. Mandor
    public function getDik()
    {
        $dik = DB::connection('sqlsrv')->table('amco_dik')->select('id', 'nama')->get();
        return response()->json($dik);
    }

    // Mengambil data berdasarkan pilihan dropdown
    public function getData(Request $request)
    {
        $kdAfd = $request->kd_afdeling;
        $regMandor = $request->reg_mandor;

        $data = DB::connection('sqlsrv')->table('amco_dik')
            ->where('afdeling_id', $kdAfd)
            ->where('mandor_id', $regMandor)
            ->get();

        return response()->json($data);
    }
}
