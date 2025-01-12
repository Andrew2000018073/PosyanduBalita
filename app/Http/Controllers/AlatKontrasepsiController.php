<?php

namespace App\Http\Controllers;

use App\Models\AlatKontrasepsi;
use App\Http\Requests\StoreAlatKontrasepsiRequest;
use App\Http\Requests\UpdateAlatKontrasepsiRequest;

class AlatKontrasepsiController extends Controller
{


    public function getAlatkontrasepsi()
    {
        return response()->json(AlatKontrasepsi::get());
    }
    public function index() {}

    public function create() {}

    public function store(StoreAlatKontrasepsiRequest $request) {}

    public function show(AlatKontrasepsi $alatKontrasepsi) {}

    public function edit(AlatKontrasepsi $alatKontrasepsi) {}

    public function update(UpdateAlatKontrasepsiRequest $request, AlatKontrasepsi $alatKontrasepsi) {}

    public function destroy(AlatKontrasepsi $alatKontrasepsi) {}
}
