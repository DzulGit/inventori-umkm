<?php

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pemasok;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper fungsi Pest untuk Laravel
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

// Wajib ditambahkan agar test berjalan sebagai Laravel App & mereset database
uses(TestCase::class, RefreshDatabase::class);

test('pengguna terautentikasi bisa melihat daftar barang', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    Barang::factory()->count(3)->create();

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/barang');

    $response->assertOk()->assertJsonCount(3, 'data');
});

test('tamu tidak bisa mengakses daftar barang', function () {
    $response = getJson('/api/barang');

    $response->assertStatus(401);
});

test('pengguna bisa menambah barang baru', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $kategori = Kategori::factory()->create();
    $pemasok = Pemasok::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/barang', [
        'kode_barang' => 'BRG-0001',
        'nama_barang' => 'Beras 5kg',
        'kategori_id' => $kategori->id,
        'pemasok_id' => $pemasok->id,
        'harga_beli' => 50000,
        'harga_jual' => 60000,
        'stok' => 20,
        'stok_minimal' => 5,
    ]);

    $response->assertCreated();
    assertDatabaseHas('barang', ['kode_barang' => 'BRG-0001']);
});

test('gagal menambah barang jika harga jual lebih kecil dari harga beli', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $kategori = Kategori::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/barang', [
        'kode_barang' => 'BRG-0002',
        'nama_barang' => 'Gula 1kg',
        'kategori_id' => $kategori->id,
        'harga_beli' => 15000,
        'harga_jual' => 10000,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('harga_jual');
});

test('gagal menambah barang jika kode_barang sudah dipakai', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $kategori = Kategori::factory()->create();
    Barang::factory()->create(['kode_barang' => 'BRG-DUP']);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/barang', [
        'kode_barang' => 'BRG-DUP',
        'nama_barang' => 'Minyak Goreng',
        'kategori_id' => $kategori->id,
        'harga_beli' => 15000,
        'harga_jual' => 18000,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('kode_barang');
});

test('pengguna bisa memperbarui barang', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $barang = Barang::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->putJson("/api/barang/{$barang->id}", [
        'kode_barang' => $barang->kode_barang,
        'nama_barang' => 'Nama Baru',
        'kategori_id' => $barang->kategori_id,
        'harga_beli' => $barang->harga_beli,
        'harga_jual' => $barang->harga_jual,
    ]);

    $response->assertOk();
    assertDatabaseHas('barang', ['id' => $barang->id, 'nama_barang' => 'Nama Baru']);
});

test('pengguna bisa menghapus barang (soft delete)', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Sekarang jadi Owner!
    $barang = Barang::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->deleteJson("/api/barang/{$barang->id}");

    $response->assertOk();
    assertSoftDeleted('barang', ['id' => $barang->id]);
});;

test('endpoint stok menipis hanya menampilkan barang di bawah stok minimal', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    Barang::factory()->create(['stok' => 2, 'stok_minimal' => 5]);
    Barang::factory()->create(['stok' => 50, 'stok_minimal' => 5]);

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/barang/stok-menipis');

    $response->assertOk()->assertJsonCount(1, 'data');
});