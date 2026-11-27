<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiLamaranRequest;
use App\Http\Requests\StoreLamaranRequest;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Notifications\LamaranBaruMasuk;
use App\Notifications\StatusLamaranDiperbarui;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LamaranController extends Controller
{
    public function index(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'user', 403);

        $statusFilter = $permintaan->query('status');
        $kueri = $permintaan->user()->lamarans()
            ->with(['lowongan.kategori', 'lowongan.perusahaan', 'jadwalWawancaraTerbaru'])
            ->latest();

        if (is_string($statusFilter) && in_array($statusFilter, Lamaran::STATUS, true)) {
            $kueri->where('status', $statusFilter);
        }

        return view('lamaran.index', [
            'daftarLamaran' => $kueri->paginate(12)->withQueryString(),
            'statusAktif' => $statusFilter,
        ]);
    }

    public function daftarPerusahaan(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'perusahaan', 403);

        $statusFilter = $permintaan->query('status');
        $lowonganFilter = $permintaan->query('lowongan');

        $kueri = Lamaran::whereHas('lowongan', fn ($q) => $q->where('perusahaan_id', $permintaan->user()->id))
            ->with(['lowongan:id,perusahaan_id,judul', 'pelamar:id,name,email,profil_pendaftaran', 'jadwalWawancaraTerbaru'])
            ->latest();

        if (is_string($statusFilter) && in_array($statusFilter, Lamaran::STATUS, true)) {
            $kueri->where('status', $statusFilter);
        }

        if (is_numeric($lowonganFilter)) {
            $kueri->where('lowongan_id', (int) $lowonganFilter);
        }

        return view('lamaran.perusahaan', [
            'daftarLamaran' => $kueri->paginate(12)->withQueryString(),
            'daftarLowonganPerusahaan' => $permintaan->user()->lowongans()->orderBy('judul')->get(['id', 'judul']),
            'statusAktif' => $statusFilter,
            'lowonganAktif' => $lowonganFilter,
        ]);
    }

    public function create(Request $permintaan, Lowongan $lowongan): View|RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'user', 403);

        if (! $lowongan->masihMenerimaLamaran()) {
            return redirect()->route('lowongan.show', $lowongan)
                ->withErrors(['lamaran' => 'Lowongan ini sudah ditutup atau melewati batas lamaran.']);
        }

        if ($lowongan->adalahEksternal() && $lowongan->sumber_url) {
            return redirect()->away($lowongan->sumber_url);
        }

        $lamaranAda = $lowongan->lamarans()->where('pelamar_id', $permintaan->user()->id)->first();
        if ($lamaranAda) {
            return redirect()->route('lamaran.show', $lamaranAda)
                ->with('status', 'Anda sudah melamar posisi ini sebelumnya.');
        }

        $lowongan->load(['kategori:id,nama', 'perusahaan:id,name']);
        $pengguna = $permintaan->user();
        $profil = $pengguna->profil_pendaftaran ?? [];

        return view('lamaran.create', [
            'lowongan' => $lowongan,
            'pengguna' => $pengguna,
            'profil' => $profil,
        ]);
    }

    public function store(StoreLamaranRequest $permintaan, Lowongan $lowongan): RedirectResponse
    {
        abort_unless($lowongan->masihMenerimaLamaran(), 422, 'Lowongan tidak menerima lamaran.');
        abort_if($lowongan->adalahEksternal(), 422, 'Lowongan eksternal dilamar melalui situs sumber.');

        $valid = $permintaan->validated();
        $pengguna = $permintaan->user();
        $direktori = "lamaran-dokumen/{$pengguna->id}";

        $berkasTersimpan = [];

        try {
            foreach (['cv', 'surat_lamaran', 'ijazah', 'portofolio', 'sertifikat'] as $kunci) {
                if ($permintaan->hasFile($kunci)) {
                    $berkasTersimpan[$kunci] = $permintaan->file($kunci)->store($direktori, 'local');
                }
            }

            $snapshotPelamar = [
                'nama_lengkap' => $valid['nama_lengkap'],
                'email' => $valid['email'],
                'telepon' => $valid['telepon'],
                'tempat_lahir' => $valid['tempat_lahir'],
                'tanggal_lahir' => $valid['tanggal_lahir'],
                'jenis_kelamin' => $valid['jenis_kelamin'],
                'domisili' => $valid['domisili'],
                'pendidikan_terakhir' => $valid['pendidikan_terakhir'],
                'pengalaman_kerja' => $valid['pengalaman_kerja'],
                'keahlian' => $valid['keahlian'],
            ];

            $lamaran = DB::transaction(function () use ($pengguna, $lowongan, $valid, $snapshotPelamar, $berkasTersimpan): Lamaran {
                $lamaran = $pengguna->lamarans()->create([
                    'lowongan_id' => $lowongan->id,
                    'status' => 'Lamaran Terkirim',
                    'catatan' => $valid['catatan'] ?? null,
                    'data_pelamar' => $snapshotPelamar,
                    'cv_path' => $berkasTersimpan['cv'] ?? null,
                    'surat_lamaran_path' => $berkasTersimpan['surat_lamaran'] ?? null,
                    'ijazah_path' => $berkasTersimpan['ijazah'] ?? null,
                    'portofolio_path' => $berkasTersimpan['portofolio'] ?? null,
                    'sertifikat_path' => $berkasTersimpan['sertifikat'] ?? null,
                ]);

                $lamaran->riwayats()->create([
                    'status_sebelumnya' => null,
                    'status_baru' => 'Lamaran Terkirim',
                    'catatan' => 'Lamaran berhasil dikirim dan diterima oleh sistem.',
                    'diubah_oleh' => $pengguna->id,
                ]);

                return $lamaran;
            });
        } catch (UniqueConstraintViolationException) {
            foreach ($berkasTersimpan as $path) {
                Storage::disk('local')->delete($path);
            }

            return back()->withInput()->withErrors(['lamaran' => 'Anda sudah melamar lowongan ini.']);
        } catch (Throwable $e) {
            foreach ($berkasTersimpan as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }

        $pengguna->notify(new StatusLamaranDiperbarui($lamaran));
        $lowongan->perusahaan->notify(new LamaranBaruMasuk($lamaran));

        return redirect()->route('lamaran.sukses', $lamaran)->with('status', 'Lamaran berhasil dikirim.');
    }

    public function sukses(Request $permintaan, Lamaran $lamaran): View
    {
        $this->authorize('view', $lamaran);
        abort_unless($lamaran->pelamar_id === $permintaan->user()->id, 403);

        $lamaran->load(['lowongan.perusahaan', 'lowongan.kategori']);

        return view('lamaran.sukses', ['lamaran' => $lamaran]);
    }

    public function show(Request $permintaan, Lamaran $lamaran): View
    {
        $this->authorize('view', $lamaran);

        $lamaran->load([
            'lowongan.kategori',
            'lowongan.perusahaan',
            'pelamar',
            'riwayats.pengubah',
            'jadwalWawancaras',
        ]);

        return view('lamaran.show', [
            'lamaran' => $lamaran,
            'adalahPerusahaan' => $permintaan->user()->role === 'perusahaan',
        ]);
    }

    public function pelamar(Request $permintaan, Lowongan $lowongan): View
    {
        $this->authorize('view', $lowongan);

        return view('lamaran.pelamar', [
            'lowongan' => $lowongan->load(['kategori', 'perusahaan', 'lamarans.pelamar', 'lamarans.jadwalWawancaraTerbaru']),
        ]);
    }

    public function update(PerbaruiLamaranRequest $permintaan, Lamaran $lamaran): RedirectResponse
    {
        $valid = $permintaan->validated();
        $statusLama = $lamaran->status;
        $statusBaru = $valid['status'];
        $catatan = $valid['catatan'] ?? null;

        DB::transaction(function () use ($lamaran, $statusLama, $statusBaru, $catatan, $permintaan): void {
            $lamaran->update(['status' => $statusBaru]);

            $lamaran->riwayats()->create([
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
                'diubah_oleh' => $permintaan->user()->id,
            ]);
        });

        $lamaran->pelamar->notify(new StatusLamaranDiperbarui($lamaran, $catatan));

        return back()->with('status', "Tahapan lamaran diperbarui menjadi {$statusBaru}.");
    }

    public function unduhCv(Request $permintaan, Lamaran $lamaran): StreamedResponse
    {
        return $this->unduhDokumen($permintaan, $lamaran, 'cv');
    }

    public function unduhDokumen(Request $permintaan, Lamaran $lamaran, string $jenis): StreamedResponse
    {
        $this->authorize('unduhDokumen', $lamaran);

        $kolomMap = [
            'cv' => 'cv_path',
            'surat_lamaran' => 'surat_lamaran_path',
            'ijazah' => 'ijazah_path',
            'portofolio' => 'portofolio_path',
            'sertifikat' => 'sertifikat_path',
        ];

        abort_unless(isset($kolomMap[$jenis]), 404);

        $path = $lamaran->{$kolomMap[$jenis]};
        abort_unless(is_string($path) && $path !== '' && Storage::disk('local')->exists($path), 404);

        $ekstensi = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $namaPelamar = $lamaran->data_pelamar['nama_lengkap'] ?? $lamaran->pelamar->name;
        $namaUnduhan = Str::upper($jenis).'-'.Str::slug($namaPelamar).'.'.$ekstensi;

        return Storage::disk('local')->download($path, $namaUnduhan);
    }
}
