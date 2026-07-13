<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Exports\LaporanStokExport;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function __construct(protected LaporanService $laporanService) {}

    public function stok(Request $request)
    {
        return response()->json(
            $this->laporanService->laporanStok($request->all())
        );
    }

    public function penjualan(Request $request)
    {
        return response()->json(
            $this->laporanService->laporanPenjualan($request->all())
        );
    }

    public function exportStok(Request $request)
    {
        return Excel::download(
            new LaporanStokExport($request->all()),
            'laporan-stok-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function exportPenjualan(Request $request)
    {
        return Excel::download(
            new LaporanPenjualanExport($request->all()),
            'laporan-penjualan-'.now()->format('Ymd-His').'.xlsx'
        );
    }
}