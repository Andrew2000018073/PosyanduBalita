<?php

namespace App\Http\Controllers;

use App\Models\ImunisasiBalita;
use App\Http\Requests\StoreImunisasiBalitaRequest;
use App\Http\Requests\UpdateImunisasiBalitaRequest;
use App\Models\Imunisasipvbalita;
use Illuminate\Http\Request;


class ImunisasiBalitaController extends Controller
{
    public function getImunisasiBalitas()
    {
        return response()->json(ImunisasiBalita::get());
    }
    public function getImunisasiBalitasUpdate($id)
    {
        return response()->json(Imunisasipvbalita::where('periksabalita_id', $id)->get());
    }
    public function index() {}

    public function deleteImunisasi($id)
    {
        ImunisasiBalita::where('id', $id)->delete();

        return response()->json('Dihapus');
    }

    public function updateImunisasi(Request $request)
    {
        $updated = ImunisasiBalita::where('id', $request->id)->update([
            'nama' => $request->nama,
            'detail' => $request->detail,
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function storeImunisasi(Request $request)
    {
        ImunisasiBalita::create([
            'nama' => $request->nama,
            'detail' => $request->detail,
        ]);
        return response()->json('berhasil');
    }
    public function create() {}

    public function store(StoreImunisasiBalitaRequest $request) {}

    public function show(ImunisasiBalita $imunisasiBalita) {}

    public function edit(ImunisasiBalita $imunisasiBalita) {}

    public function update(UpdateImunisasiBalitaRequest $request, ImunisasiBalita $imunisasiBalita) {}

    public function destroy(ImunisasiBalita $imunisasiBalita) {}
}
