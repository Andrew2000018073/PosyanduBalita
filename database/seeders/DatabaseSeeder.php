<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Wus;
use App\Models\Posyandu;
use Database\Seeders\RolePermissionSeeder as SeedersRolePermissionSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Koordinator User',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password'),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Ketuakader User',
            'email' => 'ketuakader@example.com',
            'password' => Hash::make('password'),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'kader User',
            'email' => 'kader@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->call(PosyanduSeeder::class);
        $this->call(WusSeeder::class);
        $this->call(PusSeeder::class);
        $this->call(BayiSeeder::class);
        $this->call(ImunisasiBalitaSeeder::class);
        $this->call(AlatKontrasepsiSeeder::class);
        $this->call(KegiatanPosyanduSeeder::class);
        $this->call(KehamilanSeeder::class);
        $this->call(SeedersRolePermissionSeeder::class);
        // $this->call(ImunisasiBalitaSeeder::class);
        // $this->call(PosyanduSeeder::class);
    }
}
