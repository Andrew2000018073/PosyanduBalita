<?php

namespace App\Http\Controllers;

use App\Models\Wus;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TambahPosyanduController extends Controller
{

    //
    public function wus(Request $request)
    {
        Wus::create([
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);
    }


    public function getDetail($id)
    {
        $posyandu = Posyandu::where('id', $id)->first();
        if ($posyandu) {
            return response()->json($posyandu);
        } else {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }
    }

    public function getPosyandu()
    {
        return response()->json(Posyandu::get());
    }

    public function storePosyandu(Request $request)
    {
        Posyandu::create(
            [
                'ketua_kader' => $request->ketua_kader,
                'desa' => $request->desa,
                'nama_posyandu' => $request->nama_posyandu,
                'alamat_lengkap' => $request->alamat_lengkap
            ]
        );

        return response()->json('berhasil');
    }

    public function updatePosyandu(Request $request)
    {
        $updated = Posyandu::where('id', $request->id)->update([
            'ketua_kader' => $request->ketua_kader,
            'nama_posyandu' => $request->nama_posyandu,
            'desa' => $request->desa,
            'alamat_lengkap' => $request->alamat_lengkap
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function deletePosyandu($id)
    {
        Posyandu::where('id', $id)->delete();
        return response()->json('Dihapus');
    }
}
