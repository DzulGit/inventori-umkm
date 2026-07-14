<?php

namespace Database\Seeders;

use App\Models\Pemasok;
use Illuminate\Database\Seeder;

class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        $daftarPemasok = [
            ['nama_pemasok' => 'CV Sumber Rejeki', 'kontak' => '081234567890', 'alamat' => 'Jl. Pati - Kudus No. 10'],
            ['nama_pemasok' => 'PT Distribusi Nusantara', 'kontak' => '081298765432', 'alamat' => 'Jl. Diponegoro No. 5, Pati'],
            ['nama_pemasok' => 'Toko Grosir Makmur', 'kontak' => '085612345678', 'alamat' => 'Jl. Panglima Sudirman No. 22'],
        ];

        foreach ($daftarPemasok as $pemasok) {
            Pemasok::create($pemasok);
        }
    }
}