<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $daftarKategori = [
            'Sembako',
            'Minuman',
            'Makanan Ringan',
            'Kebutuhan Rumah Tangga',
            'Alat Tulis',
        ];

        foreach ($daftarKategori as $nama) {
            Kategori::create([
                'nama_kategori' => $nama,
                'deskripsi' => "Kategori {$nama}",
            ]);
        }
    }
}