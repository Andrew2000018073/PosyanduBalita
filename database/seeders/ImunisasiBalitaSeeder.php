<?php

namespace Database\Seeders;

use App\Models\ImunisasiBalita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImunisasiBalitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            [
                'nama'=>'BCG',
                'detail'=> fake()->sentence(10)
            ],[
                'nama'=>'Polio Tetes 1',
                'detail'=>fake()->sentence(10)
            ],[
                'nama'=>'Polio Tetes 2',
                'detail'=>fake()->sentence(10)
            ],[
                'nama'=>'PCV 1',
                'detail'=>fake()->sentence(10)
            ]
            ];
           DB::table('imunisasi_balitas')->insert($data);
    }
}
