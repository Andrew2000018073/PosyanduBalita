<?php

namespace Database\Seeders;

use App\Models\PUS;
use App\Models\Wus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Wus::factory()->count(100)->create();
    }
}
