<?php

namespace App\Http\Controllers;

use App\Models\PivotKontrasepsiPus;
use App\Http\Requests\StorePivotKontrasepsiPusRequest;
use App\Http\Requests\UpdatePivotKontrasepsiPusRequest;

class PivotKontrasepsiPusController extends Controller
{

    public function getAlatPakai($id)
    {
        $alatPakai = PivotKontrasepsiPus::with('kontrasepsi')
            ->where('pus_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kontrasepsi' => $item->kontrasepsi ? $item->kontrasepsi->nama : 'N/A',
                    'status' => $item->status,
                    'tanggal_pertama_pakai' => $item->tanggal_pertama_pakai,
                    'tanggal_berhenti_pakai' => $item->tanggal_berhenti_pakai,
                ];
            });

        return response()->json($alatPakai);
    }

    public function deleteKontrasepsiPakai($id)
    {
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


    public function index() {}

    public function create() {}

    public function store(StorePivotKontrasepsiPusRequest $request) {}

    public function show(PivotKontrasepsiPus $pivotKontrasepsiPus) {}

    public function edit(PivotKontrasepsiPus $pivotKontrasepsiPus) {}

    public function update(UpdatePivotKontrasepsiPusRequest $request, PivotKontrasepsiPus $pivotKontrasepsiPus) {}

    public function destroy(PivotKontrasepsiPus $pivotKontrasepsiPus) {}
}
