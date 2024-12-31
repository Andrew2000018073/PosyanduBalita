<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablettambahDarah extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'tablet_t_d_s';
    protected $fillable = [
        'tanggal_minum',
        'kuartal',
    ];

    public function PeriksaWus(){
        return $this->belongsTo(periksawus::class);
    }

}
