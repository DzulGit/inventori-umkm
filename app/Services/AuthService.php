<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected LogAktivitasService $logAktivitas) {}

    public function login(string $email, string $password): array
    {
        $pengguna = User::where('email', $email)->first();

        if (! $pengguna || ! Hash::check($password, $pengguna->password)) {
            Log::warning('Percobaan login gagal', ['email' => $email]);

            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        $this->logAktivitas->catat($pengguna->id, 'login', 'autentikasi');

        return [
            'pengguna' => $pengguna,
            'token' => $token,
        ];
    }

    public function logout(User $pengguna): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $pengguna->currentAccessToken();

        // Amankan dari TransientToken sekaligus hilangkan garis merah Intelephense
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        $this->logAktivitas->catat($pengguna->id, 'logout', 'autentikasi');
    }

    public function gantiPassword(User $pengguna, string $passwordLama, string $passwordBaru): void
    {
        if (! Hash::check($passwordLama, $pengguna->password)) {
            throw ValidationException::withMessages([
                'password_lama' => ['Password lama tidak sesuai.'],
            ]);
        }

        $pengguna->update(['password' => Hash::make($passwordBaru)]);

        $this->logAktivitas->catat($pengguna->id, 'ganti_password', 'autentikasi');
    }
}