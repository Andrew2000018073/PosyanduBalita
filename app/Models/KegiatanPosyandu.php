<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanPosyandu extends Model
{
    use HasFactory;

    protected $fillable = [
        'posyandu_id',
        'tgl_kegiatan',
        'status_kegiatan',
        'tipe_kegiatan',
        'detail'
    ];

    // Relasi ke tabel Posyandu
    public function Posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    // Relasi ke tabel periksawus
    public function PeriksaWus()
    {
        return $this->hasMany(PeriksaWus::class, 'kegiatanposyandu_w_u_s_id');
    }

    // Relasi ke tabel periksa_balitas
    public function PeriksaBalita()
    {
        return $this->hasMany(PeriksaBalita::class, 'kegiatanposyandu_balita_id');
    }
}
