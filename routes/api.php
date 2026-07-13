<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil', [AuthController::class, 'profil']);
    Route::put('/ganti-password', [AuthController::class, 'gantiPassword']);

    Route::get('/barang/stok-menipis', [BarangController::class, 'stokMenipis']);
    Route::apiResource('barang', BarangController::class);

    Route::apiResource('transaksi', TransaksiController::class)->only(['index', 'store']);

    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('pemasok', PemasokController::class);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('laporan')->middleware('can:lihat-laporan')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok']);
        Route::get('/stok/export', [LaporanController::class, 'exportStok']);
        Route::get('/penjualan', [LaporanController::class, 'penjualan']);
        Route::get('/penjualan/export', [LaporanController::class, 'exportPenjualan']);
    });
});
