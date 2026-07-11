<?php

namespace App\Http\Controllers;

use App\Http\Requests\GantiPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $hasil = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'pengguna' => $hasil['pengguna'],
            'token' => $hasil['token'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function profil(Request $request)
    {
        return response()->json($request->user());
    }

    public function gantiPassword(GantiPasswordRequest $request)
    {
        $this->authService->gantiPassword(
            $request->user(),
            $request->validated('password_lama'),
            $request->validated('password_baru'),
        );

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}