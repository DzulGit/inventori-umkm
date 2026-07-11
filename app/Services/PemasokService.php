<?php

namespace App\Services;

use App\Models\Barang; // <-- Tambahkan import ini
use App\Models\Pemasok;
use Illuminate\Support\Facades\Log;

class PemasokService
{
    public function daftar(array $filter = [])
    {
        $query = Pemasok::query();

        if (! empty($filter['cari'])) {
            $query->where('nama_pemasok', 'like', '%'.$filter['cari'].'%');
        }

        return $query->paginate($filter['per_halaman'] ?? 15);
    }

    public function simpan(array $data, ?int $penggunaId = null): Pemasok
    {
        $pemasok = Pemasok::create($data);

        Log::info('Pemasok ditambahkan', ['pemasok_id' => $pemasok->id, 'pengguna_id' => $penggunaId]);

        return $pemasok;
    }

    public function perbarui(Pemasok $pemasok, array $data, ?int $penggunaId = null): Pemasok
    {
        $pemasok->update($data);

        Log::info('Pemasok diperbarui', ['pemasok_id' => $pemasok->id, 'pengguna_id' => $penggunaId]);

        return $pemasok;
    }

    public function hapus(Pemasok $pemasok, ?int $penggunaId = null): void
    {
        // Karena pakai SoftDeletes, kita harus set null secara manual lewat Eloquent
        // sebelum data pemasok di-soft delete.
        Barang::where('pemasok_id', $pemasok->id)->update(['pemasok_id' => null]);

        $pemasok->delete();

        Log::info('Pemasok dihapus', ['pemasok_id' => $pemasok->id, 'pengguna_id' => $penggunaId]);
    }
}