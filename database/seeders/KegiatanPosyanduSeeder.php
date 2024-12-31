<?php

namespace Database\Seeders;

use App\Models\KegiatanPosyandu;
use App\Models\PeriksaBalita;
use App\Models\periksawus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanPosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KegiatanPosyandu::factory()->count(50)->create();
        periksawus::factory()->count(500)->create();
        PeriksaBalita::factory()->count(500)->create();
    }
}
