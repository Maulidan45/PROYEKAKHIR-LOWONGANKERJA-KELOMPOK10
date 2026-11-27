<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiLowonganRequest;
use App\Http\Requests\SimpanLowonganRequest;
use App\Models\Kategori;
use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $permintaan): View
    {
        $this->authorize('viewAny', Lowongan::class);

        $permintaan->validate([
            'kata_kunci' => ['nullable', 'string', 'max:100', 'not_regex:/<\s*script\b/i'],
            'status' => ['nullable', 'in:aktif,kedaluwarsa,ditutup'],
        ]);

        $kueri = Lowongan::query()
            ->select(['id', 'kategori_id', 'perusahaan_id', 'judul', 'lokasi', 'tipe_kerja', 'gaji_min', 'gaji_max', 'batas_lamaran', 'deskripsi', 'aktif', 'sumber', 'sumber_url', 'created_at'])
            ->with(['kategori:id,nama', 'perusahaan:id,name'])
            ->withCount('lamarans')
            ->latest();

        $kueri->when(trim((string) $permintaan->query('kata_kunci')), function (Builder $kueri, string $cari): void {
            $pola = '%'.mb_strtolower($cari).'%';
            $kueri->where(
                fn (Builder $isian) => $isian->whereRaw('lower(judul) like ?', [$pola])
                    ->orWhereRaw('lower(lokasi) like ?', [$pola])
                    ->orWhereHas('perusahaan', fn (Builder $p) => $p->whereRaw('lower(name) like ?', [$pola]))
            );
        });

        match ($permintaan->query('status')) {
            'aktif' => $kueri->terbuka(),
            'kedaluwarsa' => $kueri->where('aktif', true)->whereNotNull('batas_lamaran')->whereDate('batas_lamaran', '<', today()),
            'ditutup' => $kueri->where('aktif', false),
            default => null,
        };

        if ($permintaan->user()->role !== 'perusahaan') {
            return view('lowongan.index', [
                'daftarLowongan' => $kueri->paginate(12)->withQueryString(),
            ]);
        }

        $kueri->where('perusahaan_id', $permintaan->user()->id);

        return view('lowongan.index', [
            'daftarLowongan' => $kueri->paginate(12)->withQueryString(),
            'statistik' => $this->statistikPerusahaan($permintaan->user()->id),
            'daftarKategori' => Kategori::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function show(Request $permintaan, Lowongan $lowongan): View
    {
        $bolehLihatAdminAtauPemilik = $permintaan->user()
            && ($permintaan->user()->role === 'admin' || $permintaan->user()->id === $lowongan->perusahaan_id);

        abort_unless($lowongan->masihMenerimaLamaran() || $bolehLihatAdminAtauPemilik, 404);

        $lowongan->load(['kategori:id,nama', 'perusahaan:id,name,profil_pendaftaran,status_verifikasi']);

        $lamaranPengguna = $permintaan->user()?->role === 'user'
            ? $lowongan->lamarans()->where('pelamar_id', $permintaan->user()->id)->first()
            : null;

        $sudahDisimpan = $permintaan->user()?->role === 'user'
            ? $permintaan->user()->lowonganTersimpan()->where('lowongan_id', $lowongan->id)->exists()
            : false;

        $lowonganLainPerusahaan = Lowongan::terbuka()
            ->where('perusahaan_id', $lowongan->perusahaan_id)
            ->where('id', '!=', $lowongan->id)
            ->with('kategori:id,nama')
            ->latest()
            ->limit(3)
            ->get();

        return view('lowongan.show', [
            'lowongan' => $lowongan,
            'lamaranPengguna' => $lamaranPengguna,
            'sudahMelamar' => $lamaranPengguna !== null,
            'sudahDisimpan' => $sudahDisimpan,
            'lowonganLainPerusahaan' => $lowonganLainPerusahaan,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Lowongan::class);

        return view('lowongan.form', ['lowongan' => new Lowongan, 'daftarKategori' => Kategori::orderBy('nama')->get(), 'aksi' => 'Simpan lowongan']);
    }

    public function store(SimpanLowonganRequest $permintaan): RedirectResponse
    {
        $this->authorize('create', Lowongan::class);
        $data = $permintaan->validated();
        $data['aktif'] = $permintaan->boolean('aktif');
        $permintaan->user()->lowongans()->create($data);

        return redirect()->route('lowongan.index')->with('status', 'Lowongan berhasil disimpan.');
    }

    public function edit(Lowongan $lowongan): View
    {
        $this->authorize('update', $lowongan);

        return view('lowongan.form', ['lowongan' => $lowongan, 'daftarKategori' => Kategori::orderBy('nama')->get(), 'aksi' => 'Simpan perubahan']);
    }

    public function update(PerbaruiLowonganRequest $permintaan, Lowongan $lowongan): RedirectResponse
    {
        $data = $permintaan->validated();
        $data['aktif'] = $permintaan->boolean('aktif');
        $lowongan->update($data);

        return redirect()->route('lowongan.index')->with('status', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Request $permintaan, Lowongan $lowongan): RedirectResponse
    {
        $this->authorize('delete', $lowongan);
        $lowongan->delete();

        return redirect()->route('lowongan.index')->with('status', 'Lowongan berhasil dihapus.');
    }

    private function statistikPerusahaan(int $perusahaanId): array
    {
        $lowonganPerusahaan = Lowongan::where('perusahaan_id', $perusahaanId);

        return [
            'total' => (clone $lowonganPerusahaan)->count(),
            'aktif' => (clone $lowonganPerusahaan)->terbuka()->count(),
            'kedaluwarsa' => (clone $lowonganPerusahaan)->where('aktif', true)->whereNotNull('batas_lamaran')->whereDate('batas_lamaran', '<', today())->count(),
            'lamaran' => Lamaran::whereHas('lowongan', fn (Builder $kueri) => $kueri->where('perusahaan_id', $perusahaanId))->count(),
        ];
    }
}
