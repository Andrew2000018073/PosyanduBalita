<?php

namespace App\Http\Controllers;

use App\Models\PivotKontrasepsiPus;
use App\Http\Requests\StorePivotKontrasepsiPusRequest;
use App\Http\Requests\UpdatePivotKontrasepsiPusRequest;

class PivotKontrasepsiPusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAlatPakai($id)
    {
        $alatPakai = PivotKontrasepsiPus::with('kontrasepsi')
            ->where('pus_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kontrasepsi' => $item->kontrasepsi ? $item->kontrasepsi->nama : 'N/A', // Handle null
                    'status' => $item->status,
                    'tanggal_pertama_pakai' => $item->tanggal_pertama_pakai,
                    'tanggal_berhenti_pakai' => $item->tanggal_berhenti_pakai,
                ];
            });

        return response()->json($alatPakai);
    }

    public function deleteKontrasepsiPakai($id)
    {
        // Delete related records from imunisasipvbalita (correct table name)
        // Now delete the record from periksa_balitas
        PivotKontrasepsiPus::where('id', $id)->delete();

        return response()->json('Dihapus');
    }

    public function selesaiPakai($id)
    {
        $updated = PivotKontrasepsiPus::where('id', $id)->update([
            'status' => 'Sudah diganti',
            'tanggal_berhenti_pakai' => now()->format('d-m-Y'),
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePivotKontrasepsiPusRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePivotKontrasepsiPusRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PivotKontrasepsiPus  $pivotKontrasepsiPus
     * @return \Illuminate\Http\Response
     */
    public function show(PivotKontrasepsiPus $pivotKontrasepsiPus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PivotKontrasepsiPus  $pivotKontrasepsiPus
     * @return \Illuminate\Http\Response
     */
    public function edit(PivotKontrasepsiPus $pivotKontrasepsiPus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePivotKontrasepsiPusRequest  $request
     * @param  \App\Models\PivotKontrasepsiPus  $pivotKontrasepsiPus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePivotKontrasepsiPusRequest $request, PivotKontrasepsiPus $pivotKontrasepsiPus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PivotKontrasepsiPus  $pivotKontrasepsiPus
     * @return \Illuminate\Http\Response
     */
    public function destroy(PivotKontrasepsiPus $pivotKontrasepsiPus)
    {
        //
    }
}
