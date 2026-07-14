<?php

use App\Models\Barang;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper Pest yang dibutuhkan
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function Pest\Laravel\assertDatabaseHas;

// Nyalakan mesin Laravel dan aktifkan reset database otomatis
uses(TestCase::class, RefreshDatabase::class);

test('login tercatat di log aktivitas', function () {
    $pengguna = User::factory()->create(['password' => bcrypt('password123')]);

    postJson('/api/login', [
        'email' => $pengguna->email,
        'password' => 'password123',
    ])->assertOk();

    assertDatabaseHas('log_aktivitas', [
        'pengguna_id' => $pengguna->id,
        'aktivitas' => 'login',
        'modul' => 'autentikasi',
    ]);
});

test('tambah barang tercatat di log aktivitas', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $kategori = \App\Models\Kategori::factory()->create();

    actingAs($pengguna, 'sanctum')->postJson('/api/barang', [
        'kode_barang' => 'BRG-LOG-1',
        'nama_barang' => 'Barang Uji Log',
        'kategori_id' => $kategori->id,
        'harga_beli' => 10000,
        'harga_jual' => 12000,
    ])->assertCreated();

    assertDatabaseHas('log_aktivitas', [
        'pengguna_id' => $pengguna->id,
        'aktivitas' => 'tambah_barang',
        'modul' => 'barang',
    ]);
});

test('transaksi keluar tercatat di log aktivitas', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10]);

    actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [['barang_id' => $barang->id, 'jumlah' => 2]],
    ])->assertCreated();

    assertDatabaseHas('log_aktivitas', [
        'pengguna_id' => $pengguna->id,
        'aktivitas' => 'transaksi_keluar',
        'modul' => 'transaksi',
    ]);
});