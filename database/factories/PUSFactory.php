<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pus>
 */
class PUSFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'wus_id' => $this->faker->numberBetween(1, int2: 100),
            'nama_suami' => $this->faker->name($gender = 'male'),
            'jumlah_anak_hidup' => $this->faker->randomDigit(),
            'lingkar_perut_suami' => $this->faker->randomFloat(1, 20, 30),
            // 'lingkar_perut_suami'=> $this->faker->randomFloat(),
        ];
    }
}
