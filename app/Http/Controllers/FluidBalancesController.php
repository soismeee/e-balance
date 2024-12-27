<?php

namespace App\Http\Controllers;

use App\Models\FluidBalances;
use Illuminate\Http\Request;

class FluidBalancesController extends Controller
{
    public function index(){
        return view('index', [
            'title' => 'Fluid Balances'
        ]);
    }
    
    public function balance(){
        return view('balance', [
            'title' => 'Tabel Hasil'
        ]);
    }

    public function getData(){
        $tanggal = request('tanggal');
        $data = FluidBalances::whereDate('created_at', $tanggal)->orderBy('created_at', 'desc')->get();
        if (count($data) > 0) {
            return response()->json(['statusCode' => 200, 'data' => $data]);
        } else {
            return response()->json(['statusCode' => 400,'message' => 'Data tidak ditemukan'], 400);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pasien' => 'required',
            'no_rm' => 'required',
            'infus' => 'required|numeric',
            'transfusi_darah' => 'required|numeric',
            'terapi' => 'required|numeric',
            'makan_minum_ngt' => 'required|numeric',
            'bb' => 'required|numeric', // Berat badan
            'usia' => 'required|numeric', // Usia untuk anak
            'suhu_badan' => 'required|numeric', // Suhu badan saat ini
            'urin' => 'required|numeric',
            'bab' => 'required|numeric',
            'muntah' => 'required|numeric',
            'cairan_ngt' => 'required|numeric',
            'drainage' => 'required|numeric',
        ]);

        // Air metabolisme
        if ($validated['usia'] >= 18) { // Dewasa
            $konstanta_air_metabolisme = 5; // ml/kgBB/hari
        } else {
            if ($validated['usia'] >= 12) {
                $konstanta_air_metabolisme = 5.5;
            } elseif ($validated['usia'] >= 7) {
                $konstanta_air_metabolisme = 6.5;
            } elseif ($validated['usia'] >= 5) {
                $konstanta_air_metabolisme = 8.25;
            } else {
                $konstanta_air_metabolisme = 8; // Balita
            }
        }

        $air_metabolisme = $konstanta_air_metabolisme * $validated['bb'];

        // Cairan masuk
        $cairan_masuk = $validated['infus'] 
                      + ($validated['transfusi_darah'] ?? 0)
                      + ($validated['terapi'] ?? 0)
                      + ($validated['makan_minum_ngt'] ?? 0)
                      + $air_metabolisme;

        // Cairan keluar - IWL
        if ($validated['usia'] >= 18) { // Dewasa
            $konstanta_iwl = 15;
        } else { // Anak
            $konstanta_iwl = (30 - $validated['usia']);
        }

        $iwl = $konstanta_iwl * $validated['bb'];

        // Kenaikan suhu (jika ada)
        if ($validated['suhu_badan'] > 36.8) {
            $iwl += 200 * ($validated['suhu_badan'] - 36.8);
        }

        // Cairan keluar total
        $cairan_keluar = $iwl 
                       + ($validated['urin'] ?? 0)
                       + ($validated['bab'] ?? 0)
                       + ($validated['muntah'] ?? 0)
                       + ($validated['cairan_ngt'] ?? 0)
                       + ($validated['drainage'] ?? 0)
                       + ($validated['perdarahan'] ?? 0);

        // Balance cairan
        $balance_cairan = $cairan_masuk - $cairan_keluar;

        // Simpan data
        $fluidBalance = FluidBalances::create(array_merge($validated, [
            'no_rm' => $request->no_rm,
            'pasien' => $request->pasien,
            'bb' => $request->bb,
            'usia' => $request->usia,
            'suhu_badan' => $request->suhu_badan,
            'infus' => $request->infus,
            'transfusi_darah' => $request->transfusi_darah,
            'terapi' => $request->terapi,
            'makan_minum_ngt' => $request->makan_minum_ngt,
            'urin' => $request->urin,
            'bab' => $request->bab,
            'muntah' => $request->muntah,
            'cairan_ngt' => $request->cairan_ngt,
            'drainage' => $request->drainage,
            'perdarahan' => $request->perdarahan,

            'cairan_masuk' => $cairan_masuk,
            'cairan_keluar' => $cairan_keluar,
            'iwl' => $iwl,
            'air_metabolisme' => $air_metabolisme,
            // 'balance_cairan' => $balance_cairan,
            'balance_cairan' => $balance_cairan > 0 ? '+' . $balance_cairan : $balance_cairan,
        ]));

        return response()->json(['statusCode' => 200, 'message' => 'Berhasil menghitung balance cairan', 'data' => $balance_cairan > 0 ? '+' . $balance_cairan : $balance_cairan]);
    }

    public function destroy($id){
        try {
            $fluidBalance = FluidBalances::findOrFail($id);
            $fluidBalance->delete();
            return response()->json(['statusCode' => 200, 'message' => 'Data berhasil di hapus']);
        } catch (\Throwable $th) {
            return response()->json(['statusCode' => 400, 'message' => 'Data gagal di hapus']);
        }
    }
}
