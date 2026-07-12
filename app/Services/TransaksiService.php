<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiService
{
    public function __construct(protected LogAktivitasService $logAktivitas) {}

    public function daftar(array $filter = [])
    {
        $query = Transaksi::with(['pengguna', 'detail.barang']);

        if (! empty($filter['tanggal_dari'])) {
            $query->whereDate('tanggal_transaksi', '>=', $filter['tanggal_dari']);
        }

        if (! empty($filter['tanggal_sampai'])) {
            $query->whereDate('tanggal_transaksi', '<=', $filter['tanggal_sampai']);
        }

        if (! empty($filter['jenis'])) {
            $query->where('jenis', $filter['jenis']);
        }

        if (! empty($filter['kategori_id'])) {
            $query->whereHas('detail.barang', function ($q) use ($filter) {
                $q->where('kategori_id', $filter['kategori_id']);
            });
        }

        if (! empty($filter['pemasok_id'])) {
            $query->whereHas('detail.barang', function ($q) use ($filter) {
                $q->where('pemasok_id', $filter['pemasok_id']);
            });
        }

        return $query->latest('tanggal_transaksi')->paginate($filter['per_halaman'] ?? 15);
    }

    public function simpan(array $data, int $penggunaId): Transaksi
    {
        return DB::transaction(function () use ($data, $penggunaId) {
            $totalHarga = 0;
            $barisDetail = [];

            foreach ($data['detail'] as $item) {
                $barang = Barang::lockForUpdate()->findOrFail($item['barang_id']);

                if ($data['jenis'] === 'keluar' && $barang->stok < $item['jumlah']) {
                    throw ValidationException::withMessages([
                        'detail' => ["Stok {$barang->nama_barang} tidak cukup. Sisa stok: {$barang->stok}."],
                    ]);
                }

                $hargaSatuan = $data['jenis'] === 'masuk' ? $barang->harga_beli : $barang->harga_jual;
                $subtotal = $hargaSatuan * $item['jumlah'];
                $totalHarga += $subtotal;

                $barisDetail[] = [
                    'barang_id' => $barang->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];

                $barang->stok += $data['jenis'] === 'masuk' ? $item['jumlah'] : -$item['jumlah'];
                $barang->save();
            }

            $transaksi = Transaksi::create([
                'kode_transaksi' => $this->buatKodeTransaksi($data['jenis']),
                'jenis' => $data['jenis'],
                'tanggal_transaksi' => $data['tanggal_transaksi'],
                'pengguna_id' => $penggunaId,
                'total_harga' => $totalHarga,
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $transaksi->detail()->createMany($barisDetail);

            $this->logAktivitas->catat(
                $penggunaId,
                $data['jenis'] === 'masuk' ? 'transaksi_masuk' : 'transaksi_keluar',
                'transaksi',
                "Kode: {$transaksi->kode_transaksi}, Total: {$totalHarga}"
            );

            return $transaksi->load('detail.barang');
        });
    }

    private function buatKodeTransaksi(string $jenis): string
    {
        $prefix = $jenis === 'masuk' ? 'MSK' : 'KLR';
        $tanggal = now()->format('Ymd');
        $urutan = Transaksi::whereDate('created_at', now())->where('jenis', $jenis)->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $tanggal, $urutan);
    }
}