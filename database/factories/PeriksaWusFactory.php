<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\periksawus>
 */
class PeriksaWusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'wus_id' => $this->faker->numberBetween(1,   99),
            'tablettambah_darahs_kuartal' => $this->faker->numberBetween(1,   5),
            'imunisasi_kehamilans_kuartal' => $this->faker->numberBetween(1,   5),
            'vitamin_kuartal' => $this->faker->numberBetween(1,   5),
            'kegiatanposyandu_w_u_s_id' => $this->faker->numberBetween(1,   49),
            'lila_periksa' => $this->faker->randomFloat(1, 15, 50),
            'lingkarperut_periksa' => $this->faker->randomFloat(1, 40, 120),
            'diastol' => $this->faker->numberBetween(120,   150),
            'sistol' => $this->faker->numberBetween(90,   115),
            'tinggi_fundus' => $this->faker->randomFloat(1, 25, 50),
            'keluhan' => $this->faker->paragraph(),
            'statusperiksa' => $this->faker->randomElement(['Tidak menikah', 'Hamil', 'Tidak hamil & Menyusui', 'Tidak hamil & Tidak menyusui', 'Nifas']),
        ];
    }
}
