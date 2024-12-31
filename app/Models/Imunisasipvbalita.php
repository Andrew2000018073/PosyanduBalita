<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imunisasipvbalita extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'imunisasipvbalita';
    protected $fillable = ['imunisasi_id', 'periksabalita_id'];

    public function imunisasi()
    {
        return $this->belongsToMany(ImunisasiBalita::class, 'imunisasipvbalita', 'imunisasi_id', 'periksabalita_id');
    }

    public function periksaBalita()
    {
        return $this->belongsToMany(PeriksaBalita::class, 'imunisasipvbalita', 'periksabalita_id', 'imunisasi_id');
    }
}
