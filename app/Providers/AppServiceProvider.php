<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan layanan aplikasi.
     */
    public function register(): void
    {
        //
    }

    /**
     * Siapkan layanan aplikasi.
     */
    public function boot(): void
    {
        if (! config('app.debug')) {
            Vite::useHotFile(storage_path('framework/disabled-vite.hot'));
        }

        Gate::define('view-dashboard', fn (User $pengguna, string $jenisAkun): bool => $pengguna->role === $jenisAkun);

        RateLimiter::for('login', fn (Request $permintaan): array => [
            Limit::perMinute(30)->by('login-ip:'.$permintaan->ip()),
            Limit::perMinute(5)->by('login:'.hash('sha256', strtolower(trim(is_string($permintaan->input('email')) ? $permintaan->input('email') : '')).'|'.$permintaan->ip())),
        ]);
        RateLimiter::for('register', fn (Request $permintaan): Limit => Limit::perMinute(5)->by($permintaan->ip()));
    }
}
