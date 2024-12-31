<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class periksawus extends Model
{
    use HasFactory;

    protected $fillable = [
        'wus_id',
        'tablettambah_darahs_kuartal',
        'imunisasi_kehamilans_kuartal',
        'vitamin_kuartal',
        'kegiatanposyandu_w_u_s_id',
        'lila_periksa',
        'lingkarperut_periksa',
        'diastol',
        'sistol',
        'tinggi_fundus',
        'keluhan',
        'statusperiksa'
    ];

    public function Kegiatanposyandu()
    {
        return $this->belongsTo(KegiatanPosyandu::class, 'kegiatanposyandu_w_u_s_id');
    }

    public function TablettambahDarah()
    {
        return $this->hasOne(TablettambahDarah::class);
    }

    public function ImunisasiKehamilan()
    {
        return $this->hasOne(ImunisasiKehamilan::class);
    }

    public function Wus()
    {
        return $this->belongsTo(Wus::class, 'wus_id');
    }
}
