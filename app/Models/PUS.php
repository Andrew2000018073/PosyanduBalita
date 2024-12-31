<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PUS extends Model
{
    use HasFactory;

    protected $fillable = [
        'wus_id',
        'nama_suami',
        'lingkar_perut_suami',
        'jumlah_anak_hidup',
    ];

    public function Wus()
    {
        return $this->belongsTo(Wus::class, 'wus_id');
    }

    public function Bayi()
    {
        return $this->hasMany(Bayi::class, 'pus_id');
    }

    public function alatKontrasepsis()
    {
        return $this->belongsToMany(AlatKontrasepsi::class, 'pivot_kontrasepsi_puses', 'pus_id', 'kontrasepsi_id')
            ->withPivot('status', 'tanggal_pertama_pakai', 'tanggal_berhenti_pakai')
            ->withTimestamps();
    }
}
