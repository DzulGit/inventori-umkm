<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Transaksi;

class LaporanService
{
    public function laporanStok(array $filter = [])
    {
        $query = Barang::with(['kategori', 'pemasok']);

        if (! empty($filter['kategori_id'])) {
            $query->where('kategori_id', $filter['kategori_id']);
        }

        if (! empty($filter['pemasok_id'])) {
            $query->where('pemasok_id', $filter['pemasok_id']);
        }

        if (! empty($filter['stok_menipis_saja'])) {
            $query->whereColumn('stok', '<=', 'stok_minimal');
        }

        return $query->get();
    }

    public function laporanPenjualan(array $filter = [])
    {
        $query = Transaksi::with(['pengguna', 'detail.barang.kategori'])
            ->where('jenis', 'keluar');

        if (! empty($filter['tanggal_dari'])) {
            $query->whereDate('tanggal_transaksi', '>=', $filter['tanggal_dari']);
        }

        if (! empty($filter['tanggal_sampai'])) {
            $query->whereDate('tanggal_transaksi', '<=', $filter['tanggal_sampai']);
        }

        if (! empty($filter['kategori_id'])) {
            $query->whereHas('detail.barang', function ($q) use ($filter) {
                $q->where('kategori_id', $filter['kategori_id']);
            });
        }

        return $query->latest('tanggal_transaksi')->get();
    }
}