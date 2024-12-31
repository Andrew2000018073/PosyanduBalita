<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehamilan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kehamilan_ke',
        'tanggal_awal_hamil',
        'tanggal_daftar',
        'wus_id'
    ];
    public function Wus()
    {
        return $this->belongsTo(Wus::class);
    }


}
