<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImunisasiBalita extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'detail'
    ];

    public function periksaBalita()
    {
        return $this->belongsToMany(PeriksaBalita::class, 'imunisasipvbalita', 'imunisasi_id', 'periksabalita_id');
    }

}
