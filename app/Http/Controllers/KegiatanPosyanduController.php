<?php

namespace App\Http\Controllers;

use App\Models\KegiatanPosyandu;
use App\Http\Requests\StoreKegiatanPosyanduRequest;
use App\Http\Requests\UpdateKegiatanPosyanduRequest;
use App\Models\Bayi;
use App\Models\PeriksaBalita;
use App\Models\periksawus;
use App\Models\Wus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KegiatanPosyanduController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getKegiatanBulanIni()
    {
        $currentMonth = Carbon::now()->month; // Mendapatkan bulan saat ini
        $currentYear = Carbon::now()->year;   // Mendapatkan tahun saat ini

        $kegiatanCount = KegiatanPosyandu::whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentMonth])
            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
            ->count();

        return response()->json($kegiatanCount);
    }

    public function getKegiatan($id)
    {
        // Use the $id directly from the route
        $posyandu = KegiatanPosyandu::withCount(['periksawus', 'PeriksaBalita'])
            ->where('posyandu_id', $id)
            ->get();

        if ($posyandu) {
            return response()->json($posyandu);
        } else {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }
    }
    public function getKegiatanlast5()
    {
        // Menggunakan orderByRaw untuk mengurutkan berdasarkan tgl_kegiatan yang formatnya d-m-Y
        $posyandu = KegiatanPosyandu::withCount(['periksawus', 'PeriksaBalita'])
            ->orderByRaw('STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y") DESC') // Mengubah format d-m-Y ke date untuk sorting
            ->limit(10)
            ->get();

        if ($posyandu->isEmpty()) {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }

        return response()->json($posyandu);
    }

    public function getKegiatanActive($id)
    {
        // Retrieve the posyandu records
        $posyandu = KegiatanPosyandu::where('id', $id)->get();

        // Check if the collection is empty
        if ($posyandu->isEmpty()) {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }

        // Return the records if they exist
        return response()->json($posyandu);
    }

    public function getStatus($id)
    {
        // Use the $id directly from the route
        return KegiatanPosyandu::where('id', $id)->get();
    }
    public function deleteKegiatan($id)
    {
        KegiatanPosyandu::where('id', $id)->delete();
        return response()->json('Dihapus');
    }

    public function storeKegiatan(Request $request)
    {
        $dapat = KegiatanPosyandu::create([
            'posyandu_id' => $request->posyandu_id,
            'status_kegiatan' => $request->status_kegiatan,
            'detail' => $request->detail,
            'tgl_kegiatan' => $request->tgl_kegiatan,
            'tipe_kegiatan' => $request->tipe_kegiatan
        ]);
        return response()->json([
            'success' => true,
            'id' => $dapat->id,  // return ID here
        ]);
    }

    public function updateKegiatan(Request $request)
    {
        $updated = KegiatanPosyandu::where('id', $request->id)->update([
            'status_kegiatan' => 'selesai',
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function getAbsensi($id)
    {
        $currentMonth = Carbon::now()->month; // Mendapatkan bulan saat ini
        $currentYear = Carbon::now()->year;   // Mendapatkan tahun saat ini

        $kegiatanwusselesai = KegiatanPosyandu::where('posyandu_id', $id)->where('status_kegiatan', 'selesai')->where('tipe_kegiatan', 'WUS')->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentMonth])
            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])->count();
        $afiliasiwus = Wus::where('posyandus_id', $id)->count();
        $targetpesertawus = $kegiatanwusselesai * $afiliasiwus;

        $kegiatanbalitaselesai = KegiatanPosyandu::where('posyandu_id', $id)->where('status_kegiatan', 'selesai')->where('tipe_kegiatan', 'Balita')->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentMonth])
            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])->count();
        $afiliasbalita = Bayi::where('posyandus_id', $id)->count();
        $tatgetpesertabalita = $kegiatanbalitaselesai * $afiliasbalita;

        $targetpeserta = $targetpesertawus + $tatgetpesertabalita;

        $jumlahPesertaWusTotal = 0;
        $jumlahPesertaBalitaTotal = 0;

        // Ambil semua kegiatan selesai pada bulan ini
        $kegiatanWusBulanIni = KegiatanPosyandu::where('posyandu_id', $id)
            ->where('status_kegiatan', 'selesai')
            ->where('tipe_kegiatan', 'WUS')
            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentMonth])
            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
            ->get();

        foreach ($kegiatanWusBulanIni as $kegiatan) {
            // Menghitung jumlah WUS yang terdaftar dalam periksawuses untuk setiap kegiatan
            $jumlahPesertaWusTotal += periksawus::where('kegiatanposyandu_w_u_s_id', $kegiatan->id)->count();
        }

        $kegiatanBalitaBulanIni = KegiatanPosyandu::where('posyandu_id', $id)
            ->where('status_kegiatan', 'selesai')
            ->where('tipe_kegiatan', 'Balita')
            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentMonth])
            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
            ->get();

        foreach ($kegiatanBalitaBulanIni as $kegiatan) {
            // Menghitung jumlah WUS yang terdaftar dalam periksawuses untuk setiap kegiatan
            $jumlahPesertaBalitaTotal += PeriksaBalita::where('kegiatanposyandu_balita_id', $kegiatan->id)->count();
        }

        $jumlahPesertaTotal = $jumlahPesertaWusTotal + $jumlahPesertaBalitaTotal;

        // return response()->json($jumlahPesertaTotal);
        return response()->json(compact('jumlahPesertaTotal', 'targetpeserta'));
    }

    public function getPesertabulanan($id)
    {
        $currentYear = Carbon::now()->year; // Mendapatkan tahun saat ini
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            // Hitung kegiatan WUS selesai untuk setiap bulan
            $kegiatanwusselesai = KegiatanPosyandu::where('posyandu_id', $id)
                ->where('status_kegiatan', 'selesai')
                ->where('tipe_kegiatan', 'WUS')
                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
                ->count();

            $afiliasiwus = Wus::where('posyandus_id', $id)->count();
            $targetpesertawus = $kegiatanwusselesai * $afiliasiwus;

            $jumlahPesertaWusTotal = 0;
            $kegiatanWusBulanIni = KegiatanPosyandu::where('posyandu_id', $id)
                ->where('status_kegiatan', 'selesai')
                ->where('tipe_kegiatan', 'WUS')
                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
                ->get();

            foreach ($kegiatanWusBulanIni as $kegiatan) {
                $jumlahPesertaWusTotal += PeriksaWus::where('kegiatanposyandu_w_u_s_id', $kegiatan->id)->count();
            }

            // Hitung kegiatan Balita selesai untuk setiap bulan
            $kegiatanbalitaselesai = KegiatanPosyandu::where('posyandu_id', $id)
                ->where('status_kegiatan', 'selesai')
                ->where('tipe_kegiatan', 'Balita')
                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
                ->count();

            $afiliasbalita = Bayi::where('posyandus_id', $id)->count();
            $targetpesertabalita = $kegiatanbalitaselesai * $afiliasbalita;

            $jumlahPesertaBalitaTotal = 0;
            $kegiatanBalitaBulanIni = KegiatanPosyandu::where('posyandu_id', $id)
                ->where('status_kegiatan', 'selesai')
                ->where('tipe_kegiatan', 'Balita')
                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$currentYear])
                ->get();

            foreach ($kegiatanBalitaBulanIni as $kegiatan) {
                $jumlahPesertaBalitaTotal += PeriksaBalita::where('kegiatanposyandu_balita_id', $kegiatan->id)->count();
            }

            // Simpan data tiap bulan ke dalam array
            $data[] = [
                'bulan' => $month,
                'target_peserta_wus' => $targetpesertawus,
                'real_peserta_wus' => $jumlahPesertaWusTotal,
                'target_peserta_balita' => $targetpesertabalita,
                'real_peserta_balita' => $jumlahPesertaBalitaTotal,
            ];
        }

        // Kembalikan data sebagai JSON
        return response()->json($data);
    }
}