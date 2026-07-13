<?php

namespace App\Exports;

use App\Services\LaporanService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filter = []) {}

    public function collection()
    {
        $transaksi = app(LaporanService::class)->laporanPenjualan($this->filter);

        // Pecah per baris detail supaya 1 baris Excel = 1 barang terjual
        $baris = collect();

        foreach ($transaksi as $trx) {
            foreach ($trx->detail as $detail) {
                $baris->push([
                    'kode_transaksi' => $trx->kode_transaksi,
                    'tanggal_transaksi' => $trx->tanggal_transaksi,
                    'pengguna' => $trx->pengguna->nama ?? $trx->pengguna->name ?? '-',
                    'nama_barang' => $detail->barang->nama_barang ?? '-',
                    'jumlah' => $detail->jumlah,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $detail->subtotal,
                ]);
            }
        }

        return $baris;
    }

    public function headings(): array
    {
        return [
            'Kode Transaksi', 'Tanggal', 'Kasir', 'Nama Barang', 'Jumlah', 'Harga Satuan', 'Subtotal',
        ];
    }

    public function map($baris): array
    {
        return [
            $baris['kode_transaksi'],
            $baris['tanggal_transaksi'],
            $baris['pengguna'],
            $baris['nama_barang'],
            $baris['jumlah'],
            $baris['harga_satuan'],
            $baris['subtotal'],
        ];
    }
}