<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bayi>
 */
class BayiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pus_id' => $this->faker->numberBetween(1, 100),
            'namabalita' => $this->faker->name(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-3 year', '+1 day')->format('d-m-Y'),
            'beratbadan_lahir' => $this->faker->randomFloat(),
            'panjangbadan_lahir' => $this->faker->randomFloat(),
            'likep_lahir' => $this->faker->randomFloat(),
            'posyandus_id' => $this->faker->numberBetween(1,   5),
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
        ];
    }
}
