<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PivotKontrasepsiPus extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = ['kontrasepsi_id', 'pus_id', 'status', 'tanggal_pertama_pakai', 'tanggal_berhenti_pakai'];

    public function kontrasepsi()
    {
        return $this->belongsTo(AlatKontrasepsi::class, 'kontrasepsi_id');
    }
    public function pus()
    {
        return $this->belongsTo(PUS::class, 'pus_id');
    }
}
