<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Posyandu>
 */
class PosyanduFactory extends Factory
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
            'ketua_kader' => $this->faker->name(),
            'nama_posyandu' => $this->faker->company(),
            'desa'=> $this->faker->streetName(),
            'alamat_lengkap'=>$this->faker->address(),

        ];
    }
}
