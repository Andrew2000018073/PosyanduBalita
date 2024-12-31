<?php

namespace App\Http\Controllers;

use App\Models\ImunisasiBalita;
use App\Http\Requests\StoreImunisasiBalitaRequest;
use App\Http\Requests\UpdateImunisasiBalitaRequest;
use App\Models\Imunisasipvbalita;
use Illuminate\Http\Request;


class ImunisasiBalitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImunisasiBalitas(){
        return response()->json(ImunisasiBalita::get());
    }
    public function getImunisasiBalitasUpdate($id){
        return response()->json(Imunisasipvbalita::where('periksabalita_id', $id)->get());
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function deleteImunisasi($id)
        {
            // Delete related records from imunisasipvbalita (correct table name)
            // Now delete the record from periksa_balitas
            ImunisasiBalita::where('id', $id)->delete();

            return response()->json('Dihapus');
        }

        public function updateImunisasi(Request $request){
            $updated = ImunisasiBalita::where('id', $request->id)->update([
                'nama' => $request->nama,
                'detail' => $request->detail,
            ]);

            return response()->json($updated ? 'Update successful' : 'Update failed');
        }

        public function storeImunisasi(Request $request){
            ImunisasiBalita::create([
                'nama' => $request->nama,
                'detail' => $request->detail,
            ]);
            return response()->json('berhasil');
        }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImunisasiBalitaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreImunisasiBalitaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImunisasiBalita  $imunisasiBalita
     * @return \Illuminate\Http\Response
     */
    public function show(ImunisasiBalita $imunisasiBalita)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImunisasiBalita  $imunisasiBalita
     * @return \Illuminate\Http\Response
     */
    public function edit(ImunisasiBalita $imunisasiBalita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateImunisasiBalitaRequest  $request
     * @param  \App\Models\ImunisasiBalita  $imunisasiBalita
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateImunisasiBalitaRequest $request, ImunisasiBalita $imunisasiBalita)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImunisasiBalita  $imunisasiBalita
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImunisasiBalita $imunisasiBalita)
    {
        //
    }
}
