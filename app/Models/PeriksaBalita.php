<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriksaBalita extends Model
{
    use HasFactory;

    protected $fillable = [
        'bayis_id',
        'kegiatanposyandu_balita_id',
        'panjang_badan',
        'berat_badan',
        'lingkep_periksa',
        'pemberian_asi_kuartal',
        'vitamin_kuartal',
        'catatan'
    ];

    // Relasi ke tabel kegiatan_posyandus
    public function Kegiatanposyandu()
    {
        return $this->belongsTo(KegiatanPosyandu::class, 'kegiatanposyandu_balita_id');
    }

    // Relasi ke tabel bayis
    public function bayi()
    {
        return $this->belongsTo(Bayi::class, 'bayis_id');
    }

    // Relasi many-to-many ke tabel imunisasi_balitas
    public function imunisasi()
    {
        return $this->belongsToMany(ImunisasiBalita::class, 'imunisasipvbalita', 'periksabalita_id', 'imunisasi_id');
    }
}
