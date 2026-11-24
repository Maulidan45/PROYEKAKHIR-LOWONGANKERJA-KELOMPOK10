<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiLamaranRequest;
use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        try {
            $permintaan->user()->lamarans()->create(['lowongan_id' => $lowongan->id]);
        } catch (UniqueConstraintViolationException) {
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

        return back()->with('status', 'Status lamaran diperbarui.');
    }
}
