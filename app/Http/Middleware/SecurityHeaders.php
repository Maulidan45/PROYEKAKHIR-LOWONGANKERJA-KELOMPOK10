<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Terapkan header keamanan pada tanggapan.
     *
     * @param  Closure(Request): (Response)  $lanjut
     */
    public function handle(Request $permintaan, Closure $lanjut): Response
    {
        $tanggapan = $lanjut($permintaan);
        $tanggapan->headers->set('X-Content-Type-Options', 'nosniff');
        $tanggapan->headers->set('X-Frame-Options', 'DENY');
        $tanggapan->headers->set('Referrer-Policy', 'no-referrer');
        $tanggapan->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $tanggapan->headers->set('Cache-Control', 'no-store, private');

        if (! Vite::isRunningHot()) {
            $tanggapan->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self'; form-action 'self'; base-uri 'none'; object-src 'none'; frame-ancestors 'none'");
        }

        if ($permintaan->isSecure()) {
            $tanggapan->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $tanggapan;
    }
}
