<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KegiatanPosyandu>
 */
class KegiatanPosyanduFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'posyandu_id' => $this->faker->numberBetween(1,   5),
            'tgl_kegiatan' => $this->faker->dateTimeBetween('01-01-2024', '30-10-2024')->format('d-m-Y'),
            'status_kegiatan' => $this->faker->randomElement(['selesai']),
            'tipe_kegiatan' => $this->faker->randomElement(['Balita', 'WUS']),
            'detail' => $this->faker->paragraph(),
        ];
    }
}
