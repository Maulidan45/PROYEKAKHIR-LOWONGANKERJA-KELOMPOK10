<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $perantara): void {
        $perantara->trustHosts(at: function (): array {
            $namaHost = parse_url(config('app.url'), PHP_URL_HOST);

            return in_array($namaHost, ['localhost', '127.0.0.1'], true)
                ? ['^localhost$', '^127\.0\.0\.1$']
                : ['^'.preg_quote($namaHost, '/').'$'];
        }, subdomains: false);
        $perantara->append(SecurityHeaders::class);
        $perantara->redirectUsersTo(fn (Request $permintaan): string => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $penangananKesalahan): void {
        $penangananKesalahan->render(function (ValidationException $kesalahan, Request $permintaan): ?RedirectResponse {
            if (! $permintaan->expectsJson() && $permintaan->routeIs('home', 'login.store', 'register.store', 'dashboard.role', 'lowongan.index')) {
                $rute = match ($permintaan->route()->getName()) {
                    'login.store' => 'login',
                    'register.store' => 'register',
                    'dashboard.role' => 'dashboard',
                    'lowongan.index' => 'lowongan.index',
                    default => 'home',
                };

                return redirect()->route($rute)->withErrors($kesalahan->errors())
                    ->withInput($permintaan->only([
                        'name', 'email', 'role', 'telepon', 'domisili', 'pendidikan_terakhir',
                        'bidang_usaha', 'jumlah_karyawan', 'alamat_perusahaan', 'kota_perusahaan',
                        'deskripsi_perusahaan', 'situs_perusahaan', 'nama_penanggung_jawab', 'jabatan_penanggung_jawab',
                    ]));
            }

            return null;
        });
        $penangananKesalahan->shouldRenderJsonWhen(
            fn (Request $permintaan) => $permintaan->is('api/*') || $permintaan->expectsJson(),
        );
    })->create();
