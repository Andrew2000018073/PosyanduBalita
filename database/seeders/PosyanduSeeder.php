<?php

namespace Database\Seeders;

use App\Models\Posyandu;
use Database\Factories\PosyanduFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Posyandu::factory()->count(6)->create();
    }
}
