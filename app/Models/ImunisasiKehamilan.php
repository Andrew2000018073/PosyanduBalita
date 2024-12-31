<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImunisasiKehamilan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'imunisasi_kehamilans';
    protected $fillable = [
        'posyandu_id',
        'tgl_imunisasi',
        'detail',
    ];


    public function PeriksaWus(){
        return $this->belongsTo(periksawus::class);
    }
}
