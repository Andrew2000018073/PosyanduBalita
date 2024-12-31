<?php

namespace Database\Seeders;

use App\Models\Kehamilan;
use Database\Factories\KehamilanFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KehamilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Kehamilan::factory()->count(50)->create();
    }
}
