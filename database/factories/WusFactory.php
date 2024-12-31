<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wus>
 */
class WusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'nama' => $this->faker->name($gender = 'female'),
            'statuswus' => $this->faker->randomElement(['Tidak menikah', 'Hamil', 'Tidak hamil & Menyusui', 'Tidak hamil & Tidak menyusui', 'Nifas']),
            'tanggal_lahir' => $this->faker->date('d-m-Y'),
            'lila_wus' => $this->faker->numberBetween(0, 100),
            'posyandus_id' => $this->faker->numberBetween(1,   5),

        ];
    }
}
