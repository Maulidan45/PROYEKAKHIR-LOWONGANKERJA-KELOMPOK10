<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $statusFilter = (string) $permintaan->query('status', '');
        $jenisFilter = (string) $permintaan->query('jenis', '');

        $kueri = Laporan::query()
            ->with([
                'pelapor:id,name,email,role',
                'terlaporUser:id,name,email,role,is_banned',
                'lowongan:id,judul,aktif',
            ])
            ->latest('id');

        if ($statusFilter !== '' && array_key_exists($statusFilter, Laporan::STATUS)) {
            $kueri->where('status', $statusFilter);
        }

        if ($jenisFilter !== '' && in_array($jenisFilter, ['lowongan', 'pengguna'], true)) {
            $kueri->where('jenis', $jenisFilter);
        }

        return view('admin.laporan', [
            'daftarLaporan' => $kueri->paginate(12)->withQueryString(),
            'statusAktif' => $statusFilter,
            'jenisAktif' => $jenisFilter,
            'jumlahBaru' => Laporan::where('status', 'baru')->count(),
        ]);
    }

    public function store(Request $permintaan): RedirectResponse
    {
        $pengguna = $permintaan->user();
        abort_unless(in_array($pengguna->role, ['user', 'perusahaan'], true), 403);

        $valid = $permintaan->validate([
            'jenis' => ['required', Rule::in(['lowongan', 'pengguna'])],
            'lowongan_id' => ['nullable', 'integer', 'exists:lowongans,id'],
            'terlapor_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'kategori_laporan' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:1500'],
        ], [
            'kategori_laporan.required' => 'Pilih alasan laporan terlebih dahulu.',
            'deskripsi.required' => 'Jelaskan detail masalah pada laporan Anda.',
            'deskripsi.min' => 'Detail laporan minimal :min karakter.',
        ]);

        if ($valid['jenis'] === 'lowongan') {
            abort_unless($pengguna->role === 'user' && filled($valid['lowongan_id'] ?? null), 403);
            $lowongan = Lowongan::findOrFail($valid['lowongan_id']);

            Laporan::create([
                'pelapor_id' => $pengguna->id,
                'terlapor_user_id' => $lowongan->perusahaan_id,
                'lowongan_id' => $lowongan->id,
                'jenis' => 'lowongan',
                'kategori_laporan' => $valid['kategori_laporan'],
                'deskripsi' => $valid['deskripsi'],
                'status' => 'baru',
            ]);
        } else {
            abort_unless($pengguna->role === 'perusahaan' && filled($valid['terlapor_user_id'] ?? null), 403);
            $terlapor = User::where('id', $valid['terlapor_user_id'])
                ->where('role', 'user')
                ->firstOrFail();

            Laporan::create([
                'pelapor_id' => $pengguna->id,
                'terlapor_user_id' => $terlapor->id,
                'lowongan_id' => $valid['lowongan_id'] ?? null,
                'jenis' => 'pengguna',
                'kategori_laporan' => $valid['kategori_laporan'],
                'deskripsi' => $valid['deskripsi'],
                'status' => 'baru',
            ]);
        }

        return back()->with('status', 'Laporan Anda berhasil dikirim dan akan ditinjau oleh admin.');
    }

    public function update(Request $permintaan, Laporan $laporan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $valid = $permintaan->validate([
            'status' => ['required', Rule::in(array_keys(Laporan::STATUS))],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
            'tindakan' => ['nullable', Rule::in(['none', 'nonaktifkan_lowongan', 'ban_pengguna'])],
        ]);

        $laporan->update([
            'status' => $valid['status'],
            'catatan_admin' => $valid['catatan_admin'] ?? null,
        ]);

        $tindakan = $valid['tindakan'] ?? 'none';

        if ($tindakan === 'nonaktifkan_lowongan' && $laporan->lowongan) {
            $laporan->lowongan->update(['aktif' => false]);
        } elseif ($tindakan === 'ban_pengguna' && $laporan->terlaporUser && $laporan->terlaporUser->role !== 'admin') {
            $laporan->terlaporUser->is_banned = true;
            $laporan->terlaporUser->dibanned_pada = now();
            $laporan->terlaporUser->alasan_ban = $valid['catatan_admin'] ?: "Laporan: {$laporan->kategori_laporan}";
            $laporan->terlaporUser->save();
        }

        return back()->with('status', 'Laporan berhasil diperbarui.');
    }
}
