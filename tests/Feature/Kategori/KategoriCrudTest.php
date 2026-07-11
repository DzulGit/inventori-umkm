<?php

use App\Models\Barang;
use App\Models\Kategori;
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
use function Pest\Laravel\assertDatabaseMissing;

// Nyalakan mesin Laravel dan reset database
uses(TestCase::class, RefreshDatabase::class);

test('pengguna bisa melihat daftar kategori', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    Kategori::factory()->count(3)->create();

    $response = actingAs($pengguna, 'sanctum')->getJson('/api/kategori');

    $response->assertOk()->assertJsonCount(3, 'data');
});

test('pengguna bisa menambah kategori baru', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/kategori', [
        'nama_kategori' => 'Sembako',
        'deskripsi' => 'Kebutuhan pokok sehari-hari',
    ]);

    $response->assertCreated();
    assertDatabaseHas('kategori', ['nama_kategori' => 'Sembako']);
});

test('gagal menambah kategori dengan nama duplikat', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    Kategori::factory()->create(['nama_kategori' => 'Sembako']);

    $response = actingAs($pengguna, 'sanctum')->postJson('/api/kategori', [
        'nama_kategori' => 'Sembako',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('nama_kategori');
});

test('pengguna bisa memperbarui kategori', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create();
    $kategori = Kategori::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->putJson("/api/kategori/{$kategori->id}", [
        'nama_kategori' => 'Nama Baru',
    ]);

    $response->assertOk();
    assertDatabaseHas('kategori', ['id' => $kategori->id, 'nama_kategori' => 'Nama Baru']);
});

test('kategori tidak bisa dihapus jika masih punya barang', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Ubah jadi Owner
    $kategori = Kategori::factory()->create();
    Barang::factory()->create(['kategori_id' => $kategori->id]);

    $response = actingAs($pengguna, 'sanctum')->deleteJson("/api/kategori/{$kategori->id}");

    $response->assertStatus(422);
    assertDatabaseHas('kategori', ['id' => $kategori->id]);
});

test('kategori bisa dihapus jika tidak ada barang terkait', function () {
    /** @var \App\Models\User $pengguna */
    $pengguna = User::factory()->create(['role' => 'owner']); // <-- Ubah jadi Owner
    $kategori = Kategori::factory()->create();

    $response = actingAs($pengguna, 'sanctum')->deleteJson("/api/kategori/{$kategori->id}");

    $response->assertOk();
    assertDatabaseMissing('kategori', ['id' => $kategori->id]);
});