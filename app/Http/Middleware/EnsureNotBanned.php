<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    public function handle(Request $permintaan, Closure $selanjutnya): Response
    {
        $pengguna = $permintaan->user();

        if ($pengguna && $pengguna->is_banned) {
            $alasan = $pengguna->alasan_ban ? " Alasan: {$pengguna->alasan_ban}" : '';
            Auth::logout();
            $permintaan->session()->invalidate();
            $permintaan->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => "Akun Anda telah dibanned oleh admin.{$alasan}",
            ]);
        }

        return $selanjutnya($permintaan);
    }
}
