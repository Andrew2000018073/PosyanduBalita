<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AlatKontrasepsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            [
                'nama' => 'Intra Uterine Device (IUD)',
                'detail' => fake()->sentence(10)
            ],
            [
                'nama' => 'Implan',
                'detail' => fake()->sentence(10)
            ],
            [
                'nama' => 'Kondom',
                'detail' => fake()->sentence(10)
            ],
            [
                'nama' => 'Pil',
                'detail' => fake()->sentence(10)
            ],
            [
                'nama' => 'Suntik KB',
                'detail' => fake()->sentence(10)
            ],
        ];
        DB::table('alat_kontrasepsis')->insert($data);
    }
}
