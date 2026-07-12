<?php

namespace App\Services;

use App\Models\Barang;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BarangService
{
    public function __construct(protected LogAktivitasService $logAktivitas) {}

    public function daftar(array $filter = [])
    {
        $query = Barang::with(['kategori', 'pemasok']);

        if (! empty($filter['kategori_id'])) {
            $query->where('kategori_id', $filter['kategori_id']);
        }

        if (! empty($filter['pemasok_id'])) {
            $query->where('pemasok_id', $filter['pemasok_id']);
        }

        if (! empty($filter['cari'])) {
            $query->where(function ($q) use ($filter) {
                $q->where('nama_barang', 'like', '%'.$filter['cari'].'%')
                  ->orWhere('kode_barang', 'like', '%'.$filter['cari'].'%');
            });
        }

        return $query->paginate($filter['per_halaman'] ?? 15);
    }

    public function simpan(array $data, ?UploadedFile $foto = null, ?int $penggunaId = null): Barang
    {
        if ($foto) {
            $data['foto'] = $foto->store('barang', 'public');
        }

        $data['stok'] = $data['stok'] ?? 0;
        $data['stok_minimal'] = $data['stok_minimal'] ?? 0;

        $barang = Barang::create($data);

        $this->logAktivitas->catat($penggunaId, 'tambah_barang', 'barang', "Barang: {$barang->nama_barang}");

        return $barang;
    }

    public function perbarui(Barang $barang, array $data, ?UploadedFile $foto = null, ?int $penggunaId = null): Barang
    {
        if ($foto) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $foto->store('barang', 'public');
        }

        $barang->update($data);

        $this->logAktivitas->catat($penggunaId, 'edit_barang', 'barang', "Barang: {$barang->nama_barang}");

        return $barang;
    }

    public function hapus(Barang $barang, ?int $penggunaId = null): void
    {
        $namaBarang = $barang->nama_barang;

        $barang->delete();

        $this->logAktivitas->catat($penggunaId, 'hapus_barang', 'barang', "Barang: {$namaBarang}");
    }

    public function stokMenipis()
    {
        return Barang::whereColumn('stok', '<=', 'stok_minimal')->with(['kategori', 'pemasok'])->get();
    }
}