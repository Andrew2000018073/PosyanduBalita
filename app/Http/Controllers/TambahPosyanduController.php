<?php

namespace App\Http\Controllers;

use App\Models\Wus;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TambahPosyanduController extends Controller
{

    //
    public function wus(Request $request){
        // Add validation to ensure data integrity


        // Try to create the Wus entry

            Wus::create([
                'nama' => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
            ]);
    }


    public function getDetail($id)
    {
        // Use the $id directly from the route
        $posyandu = Posyandu::where('id', $id)->first(); // Use first() to get a single record
        if ($posyandu) {
            return response()->json($posyandu);
        } else {
            return response()->json(['message' => 'Posyandu not found'], 404);
        }
    }

    public function getPosyandu(){
        return response()->json(Posyandu::get());
    }

    public function storePosyandu(Request $request){
        Posyandu::create([
            'ketua_kader' => $request->ketua_kader,
            'desa' => $request->desa,
            'nama_posyandu' => $request->nama_posyandu,
            'alamat_lengkap' => $request->alamat_lengkap
        ]
        );

        // $string = $test->desa;
        // $threeChars = substr($string, 0, 3);

        // $kodepos= 'Pos-'.$threeChars . '-' . $test->id;

        // Posyandu::where('id', $test->id)->update([
        //     'nama_posyandu'=> $kodepos,
        // ]);

        return response()->json('berhasil');
    }

    public function updatePosyandu(Request $request){
        $updated = Posyandu::where('id', $request->id)->update([
            'ketua_kader' => $request->ketua_kader,
            'nama_posyandu' => $request->nama_posyandu,
            'desa' => $request->desa,
            'alamat_lengkap' => $request->alamat_lengkap
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function deletePosyandu($id){
        Posyandu::where('id', $id)->delete();
        return response()->json('Dihapus');
    }
}
