<?php

namespace App\Services;

use App\Models\Kategori;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class KategoriService
{
    public function daftar(array $filter = [])
    {
        $query = Kategori::query();

        if (! empty($filter['cari'])) {
            $query->where('nama_kategori', 'like', '%'.$filter['cari'].'%');
        }

        return $query->paginate($filter['per_halaman'] ?? 15);
    }

    public function simpan(array $data, ?int $penggunaId = null): Kategori
    {
        $kategori = Kategori::create($data);

        Log::info('Kategori ditambahkan', ['kategori_id' => $kategori->id, 'pengguna_id' => $penggunaId]);

        return $kategori;
    }

    public function perbarui(Kategori $kategori, array $data, ?int $penggunaId = null): Kategori
    {
        $kategori->update($data);

        Log::info('Kategori diperbarui', ['kategori_id' => $kategori->id, 'pengguna_id' => $penggunaId]);

        return $kategori;
    }

    public function hapus(Kategori $kategori, ?int $penggunaId = null): void
    {
        if ($kategori->barang()->exists()) {
            throw ValidationException::withMessages([
                'kategori' => ['Kategori tidak bisa dihapus karena masih memiliki barang terkait.'],
            ]);
        }

        $kategori->delete();

        Log::info('Kategori dihapus', ['kategori_id' => $kategori->id, 'pengguna_id' => $penggunaId]);
    }
}