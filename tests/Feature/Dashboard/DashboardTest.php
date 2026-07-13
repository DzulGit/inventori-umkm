<?php

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper Pest yang dibutuhkan
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

// Hubungkan ke mesin Laravel dan aktifkan reset database otomatis
uses(TestCase::class, RefreshDatabase::class);

test('dashboard mengembalikan ringkasan yang benar', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();

    Barang::factory()->create(['stok' => 2, 'stok_minimal' => 5, 'harga_beli' => 1000]);
    Barang::factory()->create(['stok' => 50, 'stok_minimal' => 5, 'harga_beli' => 2000]);

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/dashboard');

    $response->assertOk()->assertJsonStructure([
        'total_produk', 'nilai_stok', 'jumlah_stok_menipis',
        'penjualan_hari_ini', 'penjualan_minggu_ini',
    ]);

    expect($response->json('total_produk'))->toBe(2);
    expect($response->json('jumlah_stok_menipis'))->toBe(1);
});

test('penjualan hari ini hanya menghitung transaksi keluar hari ini', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10]);

    actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [['barang_id' => $barang->id, 'jumlah' => 2]],
    ])->assertCreated();

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/dashboard');

    expect($response->json('penjualan_hari_ini'))->toBeGreaterThan(0);
});