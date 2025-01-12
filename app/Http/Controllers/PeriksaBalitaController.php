<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bayi;
use Illuminate\Http\Request;
use App\Models\PeriksaBalita;
use App\Models\KegiatanPosyandu;
use App\Models\Imunisasipvbalita;
use App\Http\Requests\StorePeriksaBalitaRequest;
use App\Http\Requests\UpdatePeriksaBalitaRequest;

class PeriksaBalitaController extends Controller
{
    public function storePesertaBalita(Request $request)
    {
        $periksaBalita = PeriksaBalita::create([
            'bayis_id' => $request->bayis_id,
            'kegiatanposyandu_balita_id' => $request->kegiatanposyandu_balita_id,
            'panjang_badan' => $request->panjang_badan,
            'berat_badan' => $request->berat_badan,
            'lingkep_periksa' => $request->lingkep_periksa,
            'pemberian_asi_kuartal' => $request->pemberian_asi_kuartal,
            'vitamin_kuartal' => $request->vitamin_kuartal,
            'catatan' => $request->catatan
        ]);
        if ($request->has('imunisasi_id')) {
            $periksaBalita->imunisasi()->attach($request->imunisasi_id);
        }
        return response()->json('berhasil');
    }





    public function updatePesertaBalita(Request $request, $id)
    {
        PeriksaBalita::where('id', $id)->update([
            'panjang_badan' => $request->panjang_badan,
            'berat_badan' => $request->berat_badan,
            'lingkep_periksa' => $request->lingkep_periksa,
            'catatan' => $request->catatan,
        ]);

        $periksaBalita = PeriksaBalita::find($id);

        if ($request->has('imunisasi_id')) {
            $periksaBalita->imunisasi()->sync($request->imunisasi_id);
        }

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }


    public function getPesertaBalita($id)
    {
        $kegiatanIds = KegiatanPosyandu::where('status_kegiatan', 'selesai')
            ->pluck('id');

        if ($kegiatanIds->isEmpty()) {
            return response()->json(['message' => 'No completed kegiatan found'], 404);
        }

        $bayis = PeriksaBalita::where('bayis_id', $id)
            ->whereIn('kegiatanposyandu_balita_id', $kegiatanIds)
            ->get();

        if ($bayis->isNotEmpty()) {
            return response()->json($bayis);
        }

        return response()->json(['message' => 'No records found for the given WUS ID and completed kegiatan'], 404);
    }



    public function getPesertaActiveBalita($id)
    {
        $peserta = PeriksaBalita::where('kegiatanposyandu_balita_id', $id)->with('bayi')->get();

        if ($peserta) {
            return response()->json($peserta);
        } else {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }
    }

    public function getNamaPeserta($id)
    {
        $peserta = PeriksaBalita::where('id', $id)->with('Bayi')->first();

        $nama = $peserta->bayi->namabalita;
        return response()->json($nama);
    }

    public function deletePesertaBalita($id)
    {
        ImunisasiPvBalita::where('periksabalita_id', $id)->delete();

        PeriksaBalita::where('id', $id)->delete();

        return response()->json('Dihapus');
    }


    public function getUmurDalamBulan($tanggalLahir)
    {
        $tanggalLahir = Carbon::parse($tanggalLahir);
        $sekarang = Carbon::now();

        $umurDalamBulan = $tanggalLahir->diffInMonths($sekarang);

        return $umurDalamBulan;
    }
}
