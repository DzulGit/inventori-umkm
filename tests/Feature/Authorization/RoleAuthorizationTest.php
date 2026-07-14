<?php

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pemasok;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Import semua helper Pest yang dibutuhkan di file ini
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

// Nyalakan mesin Laravel dan aktifkan reset database untuk file ini
uses(TestCase::class, RefreshDatabase::class);

test('staff tidak bisa menghapus barang', function () {
    /** @var \App\Models\User $staff */
    $staff = User::factory()->create(['role' => 'staff']);
    $barang = Barang::factory()->create();

    $response = actingAs($staff, 'sanctum')->deleteJson("/api/barang/{$barang->id}");

    $response->assertStatus(403);
    assertDatabaseHas('barang', ['id' => $barang->id]);
});

test('owner bisa menghapus barang', function () {
    /** @var \App\Models\User $owner */
    $owner = User::factory()->owner()->create();
    $barang = Barang::factory()->create();

    $response = actingAs($owner, 'sanctum')->deleteJson("/api/barang/{$barang->id}");

    $response->assertOk();
    assertSoftDeleted('barang', ['id' => $barang->id]);
});

test('staff tidak bisa menghapus kategori', function () {
    /** @var \App\Models\User $staff */
    $staff = User::factory()->create(['role' => 'staff']);
    $kategori = Kategori::factory()->create();

    $response = actingAs($staff, 'sanctum')->deleteJson("/api/kategori/{$kategori->id}");

    $response->assertStatus(403);
});

test('staff tidak bisa menghapus pemasok', function () {
    /** @var \App\Models\User $staff */
    $staff = User::factory()->create(['role' => 'staff']);
    $pemasok = Pemasok::factory()->create();

    $response = actingAs($staff, 'sanctum')->deleteJson("/api/pemasok/{$pemasok->id}");

    $response->assertStatus(403);
});

test('staff tidak bisa mengakses laporan', function () {
    /** @var \App\Models\User $staff */
    $staff = User::factory()->create(['role' => 'staff']);

    $response = actingAs($staff, 'sanctum')->getJson('/api/laporan/stok');

    $response->assertStatus(403);
});

test('owner bisa mengakses laporan', function () {
    /** @var \App\Models\User $owner */
    $owner = User::factory()->owner()->create();

    $response = actingAs($owner, 'sanctum')->getJson('/api/laporan/stok');

    $response->assertOk();
});

test('staff tidak bisa export laporan', function () {
    /** @var \App\Models\User $staff */
    $staff = User::factory()->create(['role' => 'staff']);

    $response = actingAs($staff, 'sanctum')->get('/api/laporan/stok/export');

    $response->assertStatus(403);
});