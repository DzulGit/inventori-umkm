<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PemasokFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_pemasok' => $this->faker->company(),
            'kontak' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
        ];
    }
}