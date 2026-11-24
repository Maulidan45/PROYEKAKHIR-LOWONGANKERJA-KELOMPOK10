<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\LowonganController;
use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $permintaan): View {
    $penyaring = $permintaan->validate([
        'kata_kunci' => ['nullable', 'string', 'max:150', 'not_regex:/<\s*script\b/i'],
        'kategori' => ['nullable', 'integer', 'exists:kategoris,id'],
        'tipe' => ['nullable', 'in:Penuh waktu,Paruh waktu,Magang,Jarak jauh'],
        'lokasi' => ['nullable', 'string', 'max:100'],
    ]);

    $kueri = Lowongan::terbuka()->with(['kategori', 'perusahaan'])->withCount('lamarans')->latest();
    $kueri->when($penyaring['kata_kunci'] ?? null, function ($kueri, string $cari): void {
        $nilaiCari = '%'.mb_strtolower($cari).'%';
        $kueri->where(function ($bagian) use ($nilaiCari): void {
            $bagian->whereRaw('lower(judul) like ?', [$nilaiCari])
                ->orWhereRaw('lower(lokasi) like ?', [$nilaiCari])
                ->orWhereHas('perusahaan', fn ($perusahaan) => $perusahaan->whereRaw('lower(name) like ?', [$nilaiCari]));
        });
    });
    $kueri->when($penyaring['kategori'] ?? null, fn ($kueri, $kategori) => $kueri->where('kategori_id', $kategori));
    $kueri->when($penyaring['tipe'] ?? null, fn ($kueri, $tipe) => $kueri->where('tipe_kerja', $tipe));
    $kueri->when($penyaring['lokasi'] ?? null, fn ($kueri, $lokasi) => $kueri->where('lokasi', $lokasi));

    return view('welcome', [
        'daftarLowongan' => $kueri->paginate(12)->withQueryString(),
        'daftarKategori' => Kategori::orderBy('nama')->get(),
        'daftarLokasi' => Lowongan::terbuka()->distinct()->orderBy('lokasi')->pluck('lokasi'),
        'cari' => $penyaring['kata_kunci'] ?? '',
        'kategori' => (string) ($penyaring['kategori'] ?? ''),
        'tipe' => $penyaring['tipe'] ?? '',
        'lokasi' => $penyaring['lokasi'] ?? '',
    ]);
})->name('home');

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
    Route::get('/lamaran', [LamaranController::class, 'index'])->name('lamaran.index');
    Route::patch('/lamaran/{lamaran}', [LamaranController::class, 'update'])->name('lamaran.update');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});
