<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function ringkasan(): array
    {
        return [
            'total_produk' => Barang::count(),
            'nilai_stok' => (float) Barang::selectRaw('COALESCE(SUM(stok * harga_beli), 0) as total')->value('total'),
            'jumlah_stok_menipis' => Barang::whereColumn('stok', '<=', 'stok_minimal')->count(),
            'penjualan_hari_ini' => $this->totalPenjualan(Carbon::today(), Carbon::today()->endOfDay()),
            'penjualan_minggu_ini' => $this->totalPenjualan(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()),
        ];
    }

    private function totalPenjualan(Carbon $dari, Carbon $sampai): float
    {
        return (float) Transaksi::where('jenis', 'keluar')
            ->whereBetween('tanggal_transaksi', [$dari, $sampai])
            ->sum('total_harga');
    }
}