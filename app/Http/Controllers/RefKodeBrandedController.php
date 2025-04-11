<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RefKodeBrandedController extends Controller
{
    public function index()
    {
        $kebun = session('selected_kebun');
    
        $afdList = DB::connection('AMCO')
            ->table('AMCO_Branded')
            ->select('kodeafd')
            ->where('kodeunit', $kebun)
            ->whereRaw("CAST(SUBSTRING(kodeafd, 4, LEN(kodeafd)) AS INT) >= 20")
            ->distinct()
            ->orderBy('kodeafd')
            ->pluck('kodeafd');
    
        return view('referensi.ref_kode_branded', compact('afdList'));
    }
    
    public function getKDAfd()
    {
        $selectedKebun = Session::get('selected_kebun');
    
        $afdelings = DB::connection('AMCO')
            ->table('AMCO_Branded')
            ->select('kodeafd')
            ->where('kodeunit', $selectedKebun)
            ->whereRaw("CAST(SUBSTRING(kodeafd, 4, LEN(kodeafd)) AS INT) >= 20")
            ->distinct()
            ->pluck('kodeafd');
    
        return response()->json($afdelings);
    }
    

    public function getData(Request $request)
    {
        $kdAfd = $request->kd_afd;
        $tanggal = $request->tanggal;
        $kebun = Session::get('selected_kebun');

        $data = DB::connection('AMCO')
            ->table('AMCO_Branded')
            ->select(
                'regmdr',
                'register',
                'nama',
                'sts',
                'jabatan',
                'kdbranded'
            )
            ->where('kodeunit', $kebun)
            ->where('kodeafd', $kdAfd)
            ->whereDate('tanggal', '>=', $tanggal)
            ->get();

        return response()->json($data);
    }
    public function update(Request $request)
{
    $updates = $request->input('updates');
    
    foreach ($updates as $row) {
        DB::connection('AMCO')
            ->table('AMCO_Branded')
            ->where('register', $row['register'])
            ->update(['kdbranded' => $row['kdbranded']]);
    }

    return response()->json(['message' => 'Berhasil disimpan']);
}

}
