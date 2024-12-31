<?php

namespace Database\Seeders;

use App\Models\Bayi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BayiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bayi::factory()->count(100)->create();
    }
}
