<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SimpanLowonganController extends Controller
{
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
