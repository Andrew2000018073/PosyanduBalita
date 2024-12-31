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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storePesertaBalita(Request $request)
    {
        // Simpan data utama di tabel periksa_balitas
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
        // Simpan data imunisasi ke tabel pivot (jika ada data imunisasi)
        if ($request->has('imunisasi_id')) {
            $periksaBalita->imunisasi()->attach($request->imunisasi_id);
        }
        return response()->json('berhasil');
    }





    public function updatePesertaBalita(Request $request, $id)
    {
        // Update data di tabel periksa_balitas
        PeriksaBalita::where('id', $id)->update([
            'panjang_badan' => $request->panjang_badan,
            'berat_badan' => $request->berat_badan,
            'lingkep_periksa' => $request->lingkep_periksa,
            'catatan' => $request->catatan,
        ]);

        // Ambil instance dari model yang baru saja di-update
        $periksaBalita = PeriksaBalita::find($id);

        // Sinkronkan data imunisasi_id dengan tabel pivot
        if ($request->has('imunisasi_id')) {
            // Menggunakan sync untuk memastikan data diperbarui tanpa duplikasi
            $periksaBalita->imunisasi()->sync($request->imunisasi_id);
        }

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }


    public function getPesertaBalita($id)
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
        $bayis = PeriksaBalita::where('bayis_id', $id)
            ->whereIn('kegiatanposyandu_balita_id', $kegiatanIds)  // Check against all completed kegiatan
            ->get();

        // If any PeriksaWus records are found, return them as JSON
        if ($bayis->isNotEmpty()) {
            return response()->json($bayis);
        }

        // If no PeriksaWus records are found, return a 404 response
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
        // Delete related records from imunisasipvbalita (correct table name)
        ImunisasiPvBalita::where('periksabalita_id', $id)->delete();

        // Now delete the record from periksa_balitas
        PeriksaBalita::where('id', $id)->delete();

        return response()->json('Dihapus');
    }


    public function getUmurDalamBulan($tanggalLahir)
    {
        // Pastikan `$tanggalLahir` adalah format yang valid (YYYY-MM-DD)
        $tanggalLahir = Carbon::parse($tanggalLahir);
        $sekarang = Carbon::now();

        // Hitung umur dalam bulan
        $umurDalamBulan = $tanggalLahir->diffInMonths($sekarang);

        return $umurDalamBulan;
    }
}
