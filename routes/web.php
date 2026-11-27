<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\JadwalWawancaraController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\NotifikasiController;
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

Route::middleware('auth')->get('/beranda', function (Request $permintaan, DasborController $dasbor, App\Services\JobMatchingService $pencocokan): View|RedirectResponse {
    $pengguna = $permintaan->user();

    if ($pengguna->role === 'admin') {
        return redirect()->route('admin.katalog.index', $permintaan->query());
    }

    if ($pengguna->role === 'perusahaan') {
        return redirect()->route('dashboard.role', ['role' => 'perusahaan']);
    }

    return $dasbor->tampil($permintaan, 'user', $pencocokan);
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
    Route::get('/lowongan/{lowongan}/lamar', [LamaranController::class, 'create'])->name('lamaran.create');
    Route::post('/lowongan/{lowongan}/lamaran', [LamaranController::class, 'store'])->name('lamaran.store');
    Route::post('/lowongan/{lowongan}/simpan', [SimpanLowonganController::class, 'toggle'])->name('lowongan.simpan');

    Route::get('/perusahaan/pelamar', [LamaranController::class, 'daftarPerusahaan'])->name('perusahaan.pelamar.index');
    Route::post('/perusahaan/iklan', function (Request $permintaan): RedirectResponse {
        abort_unless($permintaan->user()->role === 'perusahaan', 403);

        $valid = $permintaan->validate([
            'lowongan_id' => ['required', 'integer'],
            'paket' => ['required', 'in:7_hari,14_hari,30_hari'],
            'metode_pembayaran' => ['required', 'in:Transfer Bank,QRIS,E-Wallet'],
        ]);

        $lowongan = App\Models\Lowongan::where('id', $valid['lowongan_id'])
            ->where('perusahaan_id', $permintaan->user()->id)
            ->firstOrFail();

        $detailPaket = match ($valid['paket']) {
            '14_hari' => ['hari' => 14, 'label' => '14 Hari (Rp 90.000)'],
            '30_hari' => ['hari' => 30, 'label' => '30 Hari (Rp 150.000)'],
            default => ['hari' => 7, 'label' => '7 Hari (Rp 50.000)'],
        };

        $lowongan->update([
            'iklan_sampai' => now()->addDays($detailPaket['hari']),
            'iklan_paket' => $detailPaket['label'],
        ]);

        return back()->with('status', "Pembayaran ({$valid['metode_pembayaran']}) berhasil! Lowongan \"{$lowongan->judul}\" sekarang diiklankan paling atas hingga {$lowongan->iklan_sampai->format('d M Y')}.");
    })->name('perusahaan.iklan.store');
    Route::get('/perusahaan/{perusahaan}/legalitas', [AdminController::class, 'unduhLegalitas'])->name('perusahaan.legalitas');

    Route::get('/lamaran', [LamaranController::class, 'index'])->name('lamaran.index');
    Route::get('/lamaran/{lamaran}', [LamaranController::class, 'show'])->whereNumber('lamaran')->name('lamaran.show');
    Route::get('/lamaran/{lamaran}/sukses', [LamaranController::class, 'sukses'])->whereNumber('lamaran')->name('lamaran.sukses');
    Route::get('/lamaran/{lamaran}/cv', [LamaranController::class, 'unduhCv'])->name('lamaran.cv');
    Route::get('/lamaran/{lamaran}/dokumen/{jenis}', [LamaranController::class, 'unduhDokumen'])->name('lamaran.dokumen');
    Route::patch('/lamaran/{lamaran}', [LamaranController::class, 'update'])->name('lamaran.update');

    Route::get('/wawancara', [JadwalWawancaraController::class, 'index'])->name('wawancara.index');
    Route::post('/lamaran/{lamaran}/wawancara', [JadwalWawancaraController::class, 'store'])->name('wawancara.store');
    Route::patch('/wawancara/{wawancara}', [JadwalWawancaraController::class, 'update'])->name('wawancara.update');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca');
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'bacaSatu'])->name('notifikasi.baca_satu');

    Route::post('/admin/lowongan/sinkron', function (Request $permintaan, LowonganEksternalService $layanan): RedirectResponse {
        abort_unless($permintaan->user()->role === 'admin', 403);

        try {
            $jumlah = $layanan->sinkronDariRemotive(15);

            return back()->with('status', "Berhasil menarik {$jumlah} lowongan baru dari API Remotive.");
        } catch (Throwable) {
            return back()->withErrors(['sinkron' => 'Gagal menghubungi API Remotive. Silakan coba lagi nanti.']);
        }
    })->name('admin.lowongan.sinkron');
    Route::get('/admin/verifikasi', [AdminController::class, 'verifikasiIndex'])->name('admin.verifikasi.index');
    Route::patch('/admin/verifikasi/{perusahaan}', [AdminController::class, 'verifikasiUpdate'])->name('admin.verifikasi.update');
    Route::get('/admin/pengguna', [AdminController::class, 'penggunaIndex'])->name('admin.pengguna.index');
    Route::get('/admin/katalog', [AdminController::class, 'katalogIndex'])->name('admin.katalog.index');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});

Route::get('/lowongan/{lowongan}', [LowonganController::class, 'show'])
    ->whereNumber('lowongan')
    ->name('lowongan.show');
