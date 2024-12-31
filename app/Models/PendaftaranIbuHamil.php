<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranIbuHamil extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [
        'kehamilan_ke',
        'tanggal_awal_hamil',
        'tanggal_akhir_hamil',
        'tanggal_daftar',
    ];

    public function Kehamilan()
    {
        return $this->belongsTo(Kehamilan::class);
    }
}
