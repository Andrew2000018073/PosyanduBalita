<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kehamilan>
 */
class KehamilanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'kehamilan_ke' => $this->faker->randomNumber(2),
            'tanggal_awal_hamil' => $this->faker->date('d-m-Y'),
            'tanggal_daftar' => $this->faker->date('d-m-Y'),
            'wus_id' => $this->faker->numberBetween(1,   25)
        ];
    }
}
