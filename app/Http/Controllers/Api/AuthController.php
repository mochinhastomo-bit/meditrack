<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login Kurir — mengembalikan Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', 'kurir')
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda tidak aktif. Hubungi admin.',
            ], 403);
        }

        // Hapus token lama (single-device login)
        $user->tokens()->delete();

        $token = $user->createToken('kurir-app')->plainTextToken;

        $courier = $user->courier;

        return response()->json([
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'courier' => $courier ? [
                'id'           => $courier->id,
                'nik'          => $courier->nik,
                'name'         => $courier->name,
                'plate_number' => $courier->plate_number,
                'phone'        => $courier->phone,
            ] : null,
        ]);
    }

    /**
     * Logout — hapus token saat ini.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    /**
     * Profil kurir yang sedang login.
     */
    public function profile(Request $request)
    {
        $user    = $request->user()->load('courier');
        $courier = $user->courier;

        return response()->json([
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'courier' => $courier ? [
                'id'            => $courier->id,
                'nik'           => $courier->nik,
                'name'          => $courier->name,
                'plate_number'  => $courier->plate_number,
                'phone'         => $courier->phone,
                'last_latitude' => $courier->last_latitude,
                'last_longitude'=> $courier->last_longitude,
                'last_seen_at'  => $courier->last_seen_at?->toIso8601String(),
            ] : null,
        ]);
    }
}
