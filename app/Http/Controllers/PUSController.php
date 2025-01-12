<?php

namespace App\Http\Controllers;

use App\Models\PUS;
use Illuminate\Http\Request;

class PUSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getJumlahPus()
    {
        return response()->json(PUS::count());
    }
    public function getPus()
    {
        $data = PUS::with('Wus')->get();
        return response()->json($data);
    }
    public function storePus(Request $request)
    {
        PUS::create([
            'wus_id' => $request->wus_id,
            'lingkar_perut_suami' => $request->lingkar_perut_suami,
            'nama_suami' => $request->nama_suami,
            'jumlah_anak_hidup' => $request->jumlah_anak_hidup,
        ]);
        return response()->json('berhasil');
    }
    public function updatePus(Request $request)
    {
        $updated = PUS::where('id', $request->id)->update([
            'lingkar_perut_suami' => $request->lingkar_perut_suami,
            'jumlah_anak_hidup' => $request->jumlah_anak_hidup,
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function getDetail($id)
    {
        $puses = PUS::with('Wus')->where('id', $id)->first();
        if ($puses) {
            return response()->json($puses);
        } else {
            return response()->json(['message' => 'Pus not found'], 404);
        }
    }

    public function deletePus($id)
    {
        PUS::where('id', $id)->delete();
        return response()->json('Dihapus');
    }

    public function storepemakaiankontrasepsi(Request $request)
    {
        $pus = Pus::find($request->pus_id);
        $pus->alatKontrasepsis()->attach($request->kontrasepsi_id, [
            'status' => 'Sedang dipakai',
            'tanggal_pertama_pakai' => now()->format('d-m-Y'),
        ]);

        return response()->json(['message' => 'Data alat kontrasepsi berhasil ditambahkan'], 201);
    }

    public function getPusid($wus_id)
    {
        $pusIds = PUS::where('wus_id', $wus_id)->pluck('id');
        return response()->json($pusIds);
    }
}
