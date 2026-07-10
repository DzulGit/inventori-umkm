<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Pemasok;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition(): array
    {
        $hargaBeli = $this->faker->numberBetween(5000, 50000);

        return [
            'kode_barang' => strtoupper($this->faker->unique()->bothify('BRG-####')),
            'nama_barang' => $this->faker->words(3, true),
            'kategori_id' => Kategori::factory(),
            'pemasok_id' => Pemasok::factory(),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaBeli + $this->faker->numberBetween(1000, 10000),
            'stok' => $this->faker->numberBetween(10, 100),
            'stok_minimal' => 5,
            'deskripsi' => $this->faker->sentence(),
            'foto' => null,
        ];
    }
}