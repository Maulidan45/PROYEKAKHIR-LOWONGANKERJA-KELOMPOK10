<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SimpanLowonganController extends Controller
{
    public function index(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'user', 403);

        $daftarFavorit = $permintaan->user()
            ->lowonganTersimpan()
            ->with(['kategori:id,nama', 'perusahaan:id,name,foto_profil_path'])
            ->latest('simpan_lowongans.created_at')
            ->paginate(12);

        return view('user.favorit', [
            'daftarFavorit' => $daftarFavorit,
        ]);
    }

    public function toggle(Request $permintaan, Lowongan $lowongan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'user', 403);

        $perubahan = $permintaan->user()->lowonganTersimpan()->toggle($lowongan->id);
        $pesan = $perubahan['attached'] !== []
            ? 'Lowongan disimpan ke daftar favorit.'
            : 'Lowongan dihapus dari daftar favorit.';

        return back()->with('status', $pesan);
    }
}
