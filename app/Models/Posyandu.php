<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $fillable = [
        'ketua_kader',
        'desa',
        'nama_posyandu',
        'alamat_lengkap'
    ];

    public function KegiatanPosyandu()
    {
        return $this->hasMany(KegiatanPosyandu::class);
    }

    public function wus()
    {
        return $this->hasMany(Wus::class, 'posyandus_id'); // Pastikan nama kolom relasi benar
    }

    public function Bayis()
    {
        return $this->hasMany(Bayi::class, 'posyandus_id');
    }
}
