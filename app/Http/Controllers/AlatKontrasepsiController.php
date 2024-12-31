<?php

namespace App\Http\Controllers;

use App\Models\AlatKontrasepsi;
use App\Http\Requests\StoreAlatKontrasepsiRequest;
use App\Http\Requests\UpdateAlatKontrasepsiRequest;

class AlatKontrasepsiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     */


    public function getAlatkontrasepsi()
    {
        return response()->json(AlatKontrasepsi::get());
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
     * @param  \App\Http\Requests\StoreAlatKontrasepsiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlatKontrasepsiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlatKontrasepsi  $alatKontrasepsi
     * @return \Illuminate\Http\Response
     */
    public function show(AlatKontrasepsi $alatKontrasepsi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlatKontrasepsi  $alatKontrasepsi
     * @return \Illuminate\Http\Response
     */
    public function edit(AlatKontrasepsi $alatKontrasepsi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAlatKontrasepsiRequest  $request
     * @param  \App\Models\AlatKontrasepsi  $alatKontrasepsi
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlatKontrasepsiRequest $request, AlatKontrasepsi $alatKontrasepsi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlatKontrasepsi  $alatKontrasepsi
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlatKontrasepsi $alatKontrasepsi)
    {
        //
    }
}
