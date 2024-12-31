<?php

namespace App\Http\Controllers;

use App\Models\Kehamilan;
use App\Http\Requests\StoreKehamilanRequest;
use App\Http\Requests\UpdateKehamilanRequest;
use Illuminate\Http\Request;


class KehamilanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function storeKehamilan(Request $request)
    {
        Kehamilan::create([
            'kehamilan_ke' => $request->kehamilan_ke,
            'tanggal_awal_hamil' => $request->tanggal_awal_hamil,
            'tanggal_daftar' => now(),
            'wus_id' => $request->wus_id
        ]);
    }

    public function getHamil($id)
    {
        $kehamilanData = Kehamilan::where('wus_id', $id)->get();
        return response()->json($kehamilanData);
    }

    public function updateHamil($id, Request $request)
    {
        // Temukan data kehamilan berdasarkan ID
        $kehamilan = Kehamilan::find($id);

        if (!$kehamilan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kehamilan tidak ditemukan!',
            ], 404); // Return error 404 Not Found
        }

        // Update data kehamilan
        $kehamilan->tanggal_awal_hamil = $request->tanggal_awal_hamil;
        $kehamilan->tanggal_daftar = $request->tanggal_daftar;
        $kehamilan->kehamilan_ke = $request->kehamilan_ke;

        // Simpan perubahan
        $kehamilan->save();

        // Return response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Data kehamilan berhasil diperbarui',
            'data' => $kehamilan,
        ], 200); // Return success 200 OK
    }

    public function deleteHamil($id)
    {
        Kehamilan::where('id', $id)->delete();
        return response()->json('Dihapus');
    }
}
