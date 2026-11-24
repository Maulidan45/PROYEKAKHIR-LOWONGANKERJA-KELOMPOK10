<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasukApiRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AutentikasiController extends Controller
{
    public function masuk(MasukApiRequest $permintaan): PenggunaResource
    {
        $kredensial = $permintaan->validated();
        $pengguna = User::where('email', $kredensial['email'])->first();

        if (! $pengguna instanceof User || ! Hash::check($kredensial['password'], $pengguna->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak cocok.']);
        }

        $token = $pengguna->createToken($kredensial['device_name']);

        return PenggunaResource::make($pengguna)->additional(['token' => $token->plainTextToken]);
    }

    public function keluar(Request $permintaan): JsonResponse
    {
        $permintaan->user()->currentAccessToken()?->delete();

        return response()->json(status: 204);
    }

    public function saya(Request $permintaan): PenggunaResource
    {
        return PenggunaResource::make($permintaan->user());
    }
}
