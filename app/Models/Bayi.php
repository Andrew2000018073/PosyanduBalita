<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bayi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pus_id',
        'namabalita',
        'tanggal_lahir',
        'beratbadan_lahir',
        'panjangbadan_lahir',
        'likep_lahir',
        'posyandus_id',
        'jenis_kelamin',
    ];


    public function PUS()
    {
        return $this->belongsTo(PUS::class, 'pus_id');
    }

    public function PeriksaBalita()
    {
        return $this->hasMany(PeriksaBalita::class, 'bayis_id');
    }
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandus_id');
    }
}
