<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatKontrasepsi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'detail',
    ];

    public function puses()
    {
        return $this->belongsToMany(Pus::class, 'pivot_kontrasepsi_puses')
            ->withPivot('status', 'tanggal_pertama_pakai', 'tanggal_berhenti_pakai')
            ->withTimestamps();
    }
}
