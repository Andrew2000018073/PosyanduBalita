<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wus extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'statuswus',
        'lila_wus',
        'kehamilan_id',
        'posyandus_id'
    ];

    public function getUmur()
    {
        if ($this->tanggal_lahir) {
            try {
                // Konversi tanggal lahir dari format d-m-Y ke objek Carbon
                $tanggalLahir = Carbon::createFromFormat('d-m-Y', $this->tanggal_lahir);
                return $tanggalLahir->age . ' tahun';
            } catch (\Exception $e) {
                return 'Format tanggal tidak valid';
            }
        }

        return 'Tanggal lahir tidak tersedia';
    }

    public function Kehamilan()
    {
        return $this->hasMany(Kehamilan::class, 'wus_id');
    }

    public function PUS()
    {
        return $this->hasOne(PUS::class, 'wus_id');
    }

    public function periksawus()
    {
        return $this->hasMany(periksawus::class, 'wus_id');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandus_id');
    }
}
