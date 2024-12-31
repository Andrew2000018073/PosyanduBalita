<?php

namespace App\Http\Controllers;

use App\Models\KegiatanPosyandu;
use App\Models\periksawus;
use Illuminate\Http\Request;

class PeriksawusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storePesertaWus(Request $request)
    {
        periksawus::create([
            'wus_id' => $request->wus_id,
            'tablettambah_darahs_kuartal' => $request->tablettambah_darahs_kuartal,
            'imunisasi_kehamilans_kuartal' => $request->imunisasi_kehamilans_kuartal,
            'kegiatanposyandu_w_u_s_id' => $request->kegiatanposyandu_w_u_s_id,
            'lila_periksa' => $request->lila_periksa,
            'vitamin_kuartal' => $request->vitamin_kuartal,
            'lingkarperut_periksa' => $request->lingkarperut_periksa,
            'tinggi_fundus' => $request->tinggi_fundus,
            'diastol' => $request->diastol,
            'sistol' => $request->sistol,
            'keluhan' => $request->keluhan,
            'statusperiksa' => $request->statuswus
        ]);
        return response()->json('berhasil');
    }

    public function updatePesertaWus(Request $request)
    {
        $updated = periksawus::where('id', $request->id)->update([
            'wus_id' => $request->wus_id,
            'tablettambah_darahs_kuartal' => $request->tablettambah_darahs_kuartal,
            'imunisasi_kehamilans_kuartal' => $request->imunisasi_kehamilans_kuartal,
            'lila_periksa' => $request->lila_periksa,
            'vitamin_kuartal' => $request->vitamin_kuartal,
            'lingkarperut_periksa' => $request->lingkarperut_periksa,
            'diastol' => $request->diastol,
            'sistol' => $request->sistol,
            'tinggi_fundus' => $request->tinggi_fundus,
            'keluhan' => $request->keluhan
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function getPesertaActiveWus($id)
    {
        $peserta = PeriksaWus::where('kegiatanposyandu_w_u_s_id', $id)->with('Wus')->get();

        if ($peserta) {
            return response()->json($peserta);
        } else {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }
    }

    public function getNamaPeserta($id)
    {
        $peserta = PeriksaWus::where('id', $id)->with('Wus')->first();

        return response()->json($peserta);
    }

    public function deletePesertaWus($id)
    {
        periksawus::where('id', $id)->delete();
        return response()->json('Dihapus');
    }
    public function getPesertaWus($id)
    {
        // Fetch all KegiatanPosyandu with the status 'selesai'
        $kegiatanIds = KegiatanPosyandu::where('status_kegiatan', 'selesai')
            ->pluck('id');  // Get an array of all IDs with status 'selesai'

        // If no kegiatan with status 'selesai' is found, return a 404 response
        if ($kegiatanIds->isEmpty()) {
            return response()->json(['message' => 'No completed kegiatan found'], 404);
        }

        // Fetch PeriksaWus records where 'wus_id' matches the given ID
        // and the 'kegiatanposyandu_w_u_s_id' is in the completed kegiatan IDs
        $wus = PeriksaWus::where('wus_id', $id)
            ->whereIn('kegiatanposyandu_w_u_s_id', $kegiatanIds)  // Check against all completed kegiatan
            ->get();

        // If any PeriksaWus records are found, return them as JSON
        if ($wus->isNotEmpty()) {
            return response()->json($wus);
        }

        // If no PeriksaWus records are found, return a 404 response
        return response()->json(['message' => 'No records found for the given WUS ID and completed kegiatan'], 404);
    }
}
