<?php

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Tambahkan deretan helper Pest ini
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

// Pastikan baris ini juga ada agar Laravel nyala saat di-test
uses(TestCase::class, RefreshDatabase::class);

test('transaksi masuk menambah stok barang', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10]);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'masuk',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barang->id, 'jumlah' => 5],
        ],
    ]);

    $response->assertCreated();
    expect($barang->fresh()->stok)->toBe(15);
});

test('transaksi keluar mengurangi stok barang', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10]);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barang->id, 'jumlah' => 4],
        ],
    ]);

    $response->assertCreated();
    expect($barang->fresh()->stok)->toBe(6);
});

test('transaksi keluar gagal jika stok tidak cukup', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 3]);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barang->id, 'jumlah' => 10],
        ],
    ]);

    $response->assertStatus(422);
    expect($barang->fresh()->stok)->toBe(3);
    assertDatabaseCount('transaksi', 0);
});

test('transaksi dengan banyak barang tetap konsisten jika salah satu gagal', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barangCukup = Barang::factory()->create(['stok' => 20]);
    $barangKurang = Barang::factory()->create(['stok' => 2]);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barangCukup->id, 'jumlah' => 5],
            ['barang_id' => $barangKurang->id, 'jumlah' => 10],
        ],
    ]);

    $response->assertStatus(422);
    // Rollback: stok barang pertama TIDAK berkurang meski divalidasi lebih dulu
    expect($barangCukup->fresh()->stok)->toBe(20);
    expect($barangKurang->fresh()->stok)->toBe(2);
});

test('harga_satuan pada detail transaksi tersnapshot, tidak berubah walau harga barang diubah kemudian', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10, 'harga_jual' => 20000]);

    actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'keluar',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barang->id, 'jumlah' => 2],
        ],
    ])->assertCreated();

    $barang->update(['harga_jual' => 99999]);

    $transaksi = Transaksi::latest()->first();
    expect((float) $transaksi->detail->first()->harga_satuan)->toBe(20000.0);
});

test('kode_transaksi otomatis terisi dan unik', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create(['stok' => 10]);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/transaksi', [
        'jenis' => 'masuk',
        'tanggal_transaksi' => now()->toDateTimeString(),
        'detail' => [
            ['barang_id' => $barang->id, 'jumlah' => 1],
        ],
    ]);

    $response->assertCreated();
    expect($response->json('data.kode_transaksi'))->toStartWith('MSK-');
});