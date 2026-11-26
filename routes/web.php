<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\SimpanLowonganController;
use App\Models\User;
use App\Services\LowonganEksternalService;
use App\Services\WilayahService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $permintaan, WilayahService $wilayah): View|RedirectResponse {
    if ($pengguna = $permintaan->user()) {
        return redirect()->route('dashboard.role', ['role' => $pengguna->role]);
    }

    return view('welcome', AutentikasiController::dataKatalog($permintaan, $wilayah));
})->name('home');

Route::middleware('auth')->get('/beranda', function (Request $permintaan, WilayahService $wilayah): View {
    return view('welcome', AutentikasiController::dataKatalog($permintaan, $wilayah));
})->name('katalog');

Route::middleware('guest')->group(function (): void {
    Route::view('/masuk', 'auth', ['pendaftaran' => false])->name('login');
    Route::post('/masuk', [AutentikasiController::class, 'masuk'])->middleware('throttle:login')->name('login.store');
    Route::get('/daftar', fn (Request $permintaan): View|RedirectResponse => $permintaan->has('role') ? redirect()->route('register') : view('auth', ['pendaftaran' => true]))->name('register');
    Route::post('/daftar', [AutentikasiController::class, 'daftar'])->middleware('throttle:register')->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/keluar', [AutentikasiController::class, 'keluar'])->name('logout');
    Route::get('/akun', [AkunController::class, 'edit'])->name('akun.edit');
    Route::patch('/akun/profil', [AkunController::class, 'perbaruiProfil'])->name('akun.profil.update');
    Route::patch('/akun/email', [AkunController::class, 'perbaruiEmail'])->name('akun.email.update');
    Route::patch('/akun/kata-sandi', [AkunController::class, 'perbaruiKataSandi'])->name('akun.kata_sandi.update');
    Route::get('/dashboard', fn (Request $permintaan): RedirectResponse => redirect()->route('dashboard.role', ['role' => $permintaan->user()->role]))->name('dashboard');
    Route::get('/{role}/dashboard', [DasborController::class, 'tampil'])->whereIn('role', array_keys(User::JENIS_AKUN))->name('dashboard.role');
    Route::resource('lowongan', LowonganController::class)->except('show');
    Route::get('/lowongan/{lowongan}/pelamar', [LamaranController::class, 'pelamar'])->name('lowongan.pelamar');
    Route::get('/perusahaan/pelamar', [LamaranController::class, 'daftarPerusahaan'])->name('perusahaan.pelamar.index');
    Route::post('/lowongan/{lowongan}/lamaran', [LamaranController::class, 'store'])->name('lamaran.store');
    Route::post('/lowongan/{lowongan}/simpan', [SimpanLowonganController::class, 'toggle'])->name('lowongan.simpan');
    Route::get('/lamaran', [LamaranController::class, 'index'])->name('lamaran.index');
    Route::get('/lamaran/{lamaran}/cv', [LamaranController::class, 'unduhCv'])->name('lamaran.cv');
    Route::patch('/lamaran/{lamaran}', [LamaranController::class, 'update'])->name('lamaran.update');
    Route::post('/notifikasi/baca', function (Request $permintaan): RedirectResponse {
        $permintaan->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    })->name('notifikasi.baca');
    Route::post('/admin/lowongan/sinkron', function (Request $permintaan, LowonganEksternalService $layanan): RedirectResponse {
        abort_unless($permintaan->user()->role === 'admin', 403);

        try {
            $jumlah = $layanan->sinkronDariRemotive(15);

            return back()->with('status', "Berhasil menarik {$jumlah} lowongan dari API Remotive.");
        } catch (Throwable) {
            return back()->withErrors(['sinkron' => 'Gagal menghubungi API Remotive. Silakan coba lagi nanti.']);
        }
    })->name('admin.lowongan.sinkron');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});

Route::get('/lowongan/{lowongan}', [LowonganController::class, 'show'])
    ->whereNumber('lowongan')
    ->name('lowongan.show');
