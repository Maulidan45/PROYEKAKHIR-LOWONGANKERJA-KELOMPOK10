<?php

namespace App\Http\Controllers;

use App\Models\JadwalWawancara;
use App\Models\Lamaran;
use App\Notifications\JadwalWawancaraNotification;
use App\Notifications\StatusLamaranDiperbarui;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JadwalWawancaraController extends Controller
{
    public function index(Request $permintaan): View
    {
        $pengguna = $permintaan->user();
        abort_unless(in_array($pengguna->role, ['perusahaan', 'user'], true), 403);

        $daftarJadwal = $pengguna->role === 'perusahaan'
            ? $pengguna->jadwalWawancaraPerusahaan()->with(['lamaran.lowongan', 'pelamar'])->orderBy('tanggal')->orderBy('jam')->paginate(12)
            : $pengguna->jadwalWawancaraPelamar()->with(['lamaran.lowongan', 'perusahaan'])->orderBy('tanggal')->orderBy('jam')->paginate(12);

        $daftarPelamarSiap = $pengguna->role === 'perusahaan'
            ? Lamaran::whereHas('lowongan', fn ($q) => $q->where('perusahaan_id', $pengguna->id))
                ->whereNotIn('status', ['Diterima', 'Ditolak'])
                ->with(['lowongan:id,judul', 'pelamar:id,name'])
                ->latest()
                ->get()
            : collect();

        return view('wawancara.index', [
            'daftarJadwal' => $daftarJadwal,
            'daftarPelamarSiap' => $daftarPelamarSiap,
            'adalahPerusahaan' => $pengguna->role === 'perusahaan',
        ]);
    }

    public function store(Request $permintaan, Lamaran $lamaran): RedirectResponse
    {
        $this->authorize('update', $lamaran);

        $valid = $permintaan->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'tipe' => ['required', Rule::in(JadwalWawancara::TIPE)],
            'lokasi_atau_tautan' => ['required', 'string', 'max:500', 'not_regex:/\x00/'],
            'catatan' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
        ], [
            'required' => ':attribute wajib diisi.',
            'after_or_equal' => 'Tanggal wawancara minimal hari ini.',
            'jam.regex' => 'Format jam harus JJ:MM (contoh 09:30).',
        ], [
            'tanggal' => 'Tanggal wawancara',
            'jam' => 'Jam wawancara',
            'tipe' => 'Tipe wawancara',
            'lokasi_atau_tautan' => 'Lokasi atau tautan',
            'catatan' => 'Pesan untuk pelamar',
        ]);

        $jadwal = DB::transaction(function () use ($permintaan, $lamaran, $valid): JadwalWawancara {
            $jadwal = JadwalWawancara::create([
                'lamaran_id' => $lamaran->id,
                'perusahaan_id' => $permintaan->user()->id,
                'pelamar_id' => $lamaran->pelamar_id,
                'tanggal' => $valid['tanggal'],
                'jam' => $valid['jam'],
                'tipe' => $valid['tipe'],
                'lokasi_atau_tautan' => $valid['lokasi_atau_tautan'],
                'catatan' => $valid['catatan'] ?? null,
                'status' => 'Dijadwalkan',
            ]);

            if ($lamaran->status !== 'Wawancara') {
                $statusLama = $lamaran->status;
                $lamaran->update(['status' => 'Wawancara']);
                $lamaran->riwayats()->create([
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => 'Wawancara',
                    'catatan' => "Jadwal wawancara dibuat pada {$valid['tanggal']} pukul {$valid['jam']}.",
                    'diubah_oleh' => $permintaan->user()->id,
                ]);
                $lamaran->pelamar->notify(new StatusLamaranDiperbarui($lamaran));
            }

            return $jadwal;
        });

        $lamaran->pelamar->notify(new JadwalWawancaraNotification($jadwal, false));

        return back()->with('status', 'Jadwal wawancara berhasil dibuat dan dikirim ke pelamar.');
    }

    public function update(Request $permintaan, JadwalWawancara $wawancara): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'perusahaan' && $wawancara->perusahaan_id === $permintaan->user()->id, 403);

        $valid = $permintaan->validate([
            'tanggal' => ['required', 'date'],
            'jam' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'tipe' => ['required', Rule::in(JadwalWawancara::TIPE)],
            'lokasi_atau_tautan' => ['required', 'string', 'max:500', 'not_regex:/\x00/'],
            'catatan' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
            'status' => ['required', Rule::in(JadwalWawancara::STATUS)],
        ]);

        $wawancara->update($valid);
        $wawancara->pelamar->notify(new JadwalWawancaraNotification($wawancara, true));

        return back()->with('status', 'Jadwal wawancara berhasil diperbarui.');
    }
}
