<?php

use App\Models\Barang;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper Pest yang dibutuhkan
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

// Hubungkan ke mesin Laravel dan aktifkan reset database otomatis
uses(TestCase::class, RefreshDatabase::class);

test('laporan stok bisa difilter stok menipis saja', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Jadi Owner
    Barang::factory()->create(['stok' => 2, 'stok_minimal' => 5]);
    Barang::factory()->create(['stok' => 50, 'stok_minimal' => 5]);

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/laporan/stok?stok_menipis_saja=1');

    $response->assertOk();
    expect($response->json())->toHaveCount(1);
});

test('export laporan stok mengembalikan file excel', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Jadi Owner
    Barang::factory()->count(2)->create();

    $response = actingAs($pengguna, 'sanctum')->get('/api/laporan/stok/export');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('export laporan penjualan mengembalikan file excel', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Jadi Owner
    $barang = Barang::factory()->create(['stok' => 10]);

    actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [['barang_id' => $barang->id, 'jumlah' => 1]],
    ])->assertCreated();

    $response = actingAs($pengguna, 'sanctum')->get('/api/laporan/penjualan/export');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});