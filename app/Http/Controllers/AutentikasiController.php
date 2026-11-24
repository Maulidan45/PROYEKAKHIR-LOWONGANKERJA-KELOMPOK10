<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermintaanPendaftaran;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AutentikasiController extends Controller
{
    public function daftar(PermintaanPendaftaran $permintaan): RedirectResponse
    {
        $data = $permintaan->validated();
        $pengguna = new User;
        $pengguna->fill(Arr::only($data, ['name', 'email', 'password']));
        $pengguna->role = $data['role'];
        $pengguna->profil_pendaftaran = Arr::except($data, ['name', 'email', 'password', 'role']);
        try {
            $pengguna->save();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Email sudah terdaftar. Silakan masuk.']);
        }

        Auth::login($pengguna);
        $permintaan->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function masuk(Request $permintaan): RedirectResponse
    {
        if (is_string($permintaan->input('email'))) {
            $permintaan->merge(['email' => strtolower(trim($permintaan->input('email')))]);
        }

        $kredensial = $permintaan->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:1000'],
        ], ['required' => ':attribute wajib diisi.', 'string' => ':attribute harus berupa teks.', 'max.string' => ':attribute maksimal :max karakter.', 'email.email' => 'Masukkan alamat email yang valid.'],
            ['email' => 'Email', 'password' => 'Kata sandi']);

        if (strlen($kredensial['password']) > 72 || str_contains($kredensial['password'], "\0") || ! Auth::attempt($kredensial)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak cocok.']);
        }

        $permintaan->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function keluar(Request $permintaan): RedirectResponse
    {
        Auth::logout();
        $permintaan->session()->invalidate();
        $permintaan->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah keluar dari akun.');
    }
}
