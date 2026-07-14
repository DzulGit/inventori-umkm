<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pemasok;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriSembako = Kategori::where('nama_kategori', 'Sembako')->first();
        $kategoriMinuman = Kategori::where('nama_kategori', 'Minuman')->first();
        $pemasok = Pemasok::first();

        $daftarBarang = [
            [
                'kode_barang' => 'BRG-0001',
                'nama_barang' => 'Beras 5kg',
                'kategori_id' => $kategoriSembako->id,
                'pemasok_id' => $pemasok->id,
                'harga_beli' => 55000,
                'harga_jual' => 65000,
                'stok' => 40,
                'stok_minimal' => 10,
                'deskripsi' => 'Beras kualitas premium kemasan 5kg',
            ],
            [
                'kode_barang' => 'BRG-0002',
                'nama_barang' => 'Minyak Goreng 1L',
                'kategori_id' => $kategoriSembako->id,
                'pemasok_id' => $pemasok->id,
                'harga_beli' => 14000,
                'harga_jual' => 17000,
                'stok' => 3,
                'stok_minimal' => 10,
                'deskripsi' => 'Minyak goreng kemasan 1 liter',
            ],
            [
                'kode_barang' => 'BRG-0003',
                'nama_barang' => 'Teh Botol 350ml',
                'kategori_id' => $kategoriMinuman->id,
                'pemasok_id' => $pemasok->id,
                'harga_beli' => 3000,
                'harga_jual' => 4500,
                'stok' => 100,
                'stok_minimal' => 20,
                'deskripsi' => 'Teh manis dalam botol 350ml',
            ],
        ];

        foreach ($daftarBarang as $barang) {
            Barang::create($barang);
        }
    }
}