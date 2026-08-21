<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Password nggak di-Hash::make manual di sini.
        // Model User punya cast 'hashed', jadi hashing-nya jalan otomatis waktu disimpan.
        $user = User::create($data);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $this->bentukUser($user),
            'token' => $user->createToken('api_token')->plainTextToken,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $kredensial['email'])->first();

        // Pesan buat email nggak ada dan password salah sengaja disamain,
        // biar orang luar nggak bisa nebak email mana yang kedaftar.
        if (! $user || ! Hash::check($kredensial['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ])->status(401);
        }

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $this->bentukUser($user),
            'token' => $user->createToken('api_token')->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Cuma token yang lagi dipakai yang dicabut.
        // Kalau user login di HP dan laptop, logout di satu perangkat nggak nendang yang lain.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->bentukUser($request->user()),
        ]);
    }

    private function bentukUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
