<?php

namespace App\Exports;

use App\Models\Posyandu;
use Maatwebsite\Excel\Concerns\FromCollection;

class RegisterIbuhamilExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Posyandu::all();
    }
}
