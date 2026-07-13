<?php

namespace App\Exports;

use App\Services\LaporanService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanStokExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filter = []) {}

    public function collection()
    {
        return app(LaporanService::class)->laporanStok($this->filter);
    }

    public function headings(): array
    {
        return [
            'Kode Barang', 'Nama Barang', 'Kategori', 'Pemasok',
            'Harga Beli', 'Harga Jual', 'Stok', 'Stok Minimal', 'Status',
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->kategori->nama_kategori ?? '-',
            $barang->pemasok->nama_pemasok ?? '-',
            $barang->harga_beli,
            $barang->harga_jual,
            $barang->stok,
            $barang->stok_minimal,
            $barang->stok <= $barang->stok_minimal ? 'Stok Menipis' : 'Aman',
        ];
    }
}