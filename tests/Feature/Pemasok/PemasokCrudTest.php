<?php

use App\Models\Barang;
use App\Models\Pemasok;
use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper Pest
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

// Nyalakan mesin Laravel dan reset database
uses(TestCase::class, RefreshDatabase::class);

test('pengguna bisa melihat daftar pemasok', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    Pemasok::factory()->count(2)->create();

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/pemasok');

    $response->assertOk()->assertJsonCount(2, 'data');
});

test('pengguna bisa menambah pemasok baru', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/pemasok', [
        'nama_pemasok' => 'CV Sumber Rejeki',
        'kontak' => '081234567890',
        'alamat' => 'Jl. Pati - Kudus No. 10',
    ]);

    $response->assertCreated();
    assertDatabaseHas('pemasok', ['nama_pemasok' => 'CV Sumber Rejeki']);
});

test('pengguna bisa memperbarui pemasok', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $pemasok = Pemasok::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->putJson("/api/pemasok/{$pemasok->id}", [
        'nama_pemasok' => 'Nama Baru',
    ]);

    $response->assertOk();
    assertDatabaseHas('pemasok', ['id' => $pemasok->id, 'nama_pemasok' => 'Nama Baru']);
});

test('menghapus pemasok tidak menghapus barang, tapi pemasok_id barang jadi null', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Ubah jadi Owner
    $pemasok = Pemasok::factory()->create();
    $barang = Barang::factory()->create(['pemasok_id' => $pemasok->id]);

    $response = actingAs($pengguna, 'sanctum')->deleteJson("/api/pemasok/{$pemasok->id}");

    $response->assertOk();
    assertSoftDeleted('pemasok', ['id' => $pemasok->id]);
    expect($barang->fresh()->pemasok_id)->toBeNull();
});