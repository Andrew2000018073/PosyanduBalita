<?php

namespace Database\Seeders;

use App\Models\PUS;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PUS::factory()->count(100)->create();
    }
}
