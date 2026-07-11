<?php

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;

// Baris ini adalah kuncinya! Mengaktifkan fitur Laravel dan mereset database khusus untuk file ini.
uses(TestCase::class, RefreshDatabase::class);

test('pengguna bisa login dengan kredensial benar', function () {
    $pengguna = User::factory()->create(['password' => bcrypt('password123')]);

    $response = postJson('/api/login', [
        'email' => $pengguna->email,
        'password' => 'password123',
    ]);

    $response->assertOk()->assertJsonStructure(['pengguna', 'token']);
});

test('login gagal dengan password salah', function () {
    $pengguna = User::factory()->create(['password' => bcrypt('password123')]);

    $response = postJson('/api/login', [
        'email' => $pengguna->email,
        'password' => 'salah',
    ]);

    $response->assertStatus(422);
});

test('endpoint profil butuh autentikasi', function () {
    $response = getJson('/api/profil');

    $response->assertStatus(401);
});