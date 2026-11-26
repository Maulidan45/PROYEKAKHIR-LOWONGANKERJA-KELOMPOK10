<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiLamaranRequest;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Notifications\StatusLamaranDiperbarui;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LamaranController extends Controller
{
    public function index(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'user', 403);

        return view('lamaran.index', ['daftarLamaran' => $permintaan->user()->lamarans()->with(['lowongan.kategori', 'lowongan.perusahaan'])->latest()->paginate(12)]);
    }

    public function daftarPerusahaan(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'perusahaan', 403);

        return view('lamaran.perusahaan', [
            'daftarLamaran' => Lamaran::whereHas('lowongan', fn ($kueri) => $kueri->where('perusahaan_id', $permintaan->user()->id))
                ->with(['lowongan:id,perusahaan_id,judul', 'pelamar:id,name,email'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function store(Request $permintaan, Lowongan $lowongan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'user', 403);
        abort_unless($lowongan->aktif && $lowongan->batas_lamaran->gte(today()), 422, 'Lowongan tidak menerima lamaran.');

        $valid = $permintaan->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        $cvPath = $permintaan->hasFile('cv')
            ? $permintaan->file('cv')->store('cv-lamaran', 'local')
            : null;

        try {
            $permintaan->user()->lamarans()->create([
                'lowongan_id' => $lowongan->id,
                'catatan' => $valid['catatan'] ?? null,
                'cv_path' => $cvPath,
            ]);
        } catch (UniqueConstraintViolationException) {
            if ($cvPath !== null) {
                Storage::disk('local')->delete($cvPath);
            }

            return back()->withErrors(['lamaran' => 'Kamu sudah melamar lowongan ini.']);
        }

        return redirect()->route('lamaran.index')->with('status', 'Lamaran berhasil dikirim.');
    }

    public function pelamar(Request $permintaan, Lowongan $lowongan): View
    {
        $this->authorize('view', $lowongan);

        return view('lamaran.pelamar', ['lowongan' => $lowongan->load('lamarans.pelamar')]);
    }

    public function update(PerbaruiLamaranRequest $permintaan, Lamaran $lamaran): RedirectResponse
    {
        $lamaran->update($permintaan->validated());
        $lamaran->pelamar->notify(new StatusLamaranDiperbarui($lamaran));

        return back()->with('status', 'Status lamaran diperbarui.');
    }

    public function unduhCv(Request $permintaan, Lamaran $lamaran): StreamedResponse
    {
        $pengguna = $permintaan->user();
        $berhak = $pengguna->role === 'admin'
            || $lamaran->pelamar_id === $pengguna->id
            || $lamaran->lowongan->perusahaan_id === $pengguna->id;

        abort_unless($berhak, 403);
        abort_unless($lamaran->cv_path && Storage::disk('local')->exists($lamaran->cv_path), 404);

        $namaUnduhan = 'CV-'.Str::slug($lamaran->pelamar->name).'.pdf';

        return Storage::disk('local')->download($lamaran->cv_path, $namaUnduhan);
    }
}
