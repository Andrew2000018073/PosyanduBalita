<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriksaBalita>
 */
class PeriksaBalitaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'bayis_id' => $this->faker->numberBetween(1,   99),
            'kegiatanposyandu_balita_id' => $this->faker->numberBetween(1,   49),
            'panjang_badan' => $this->faker->randomFloat(1, 49, 115),
            'berat_badan' => $this->faker->randomFloat(1, 1, 30),
            'lingkep_periksa' => $this->faker->randomFloat(1, 25, 90),
            'pemberian_asi_kuartal' => $this->faker->randomDigit(0, 6),
            'vitamin_kuartal' => $this->faker->randomDigit(0, 2),
            'catatan' => $this->faker->paragraph(),
        ];
    }
}
