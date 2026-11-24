<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        return view('kategori.index', ['daftarKategori' => Kategori::withCount('lowongans')->orderBy('nama')->paginate(20)]);
    }

    public function store(SimpanKategoriRequest $permintaan): RedirectResponse
    {
        Kategori::create($permintaan->validated());

        return redirect()->route('kategori.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function destroy(Request $permintaan, Kategori $kategori): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin', 403);
        abort_if($kategori->lowongans()->exists(), 422, 'Kategori masih digunakan oleh lowongan.');
        $kategori->delete();

        return redirect()->route('kategori.index')->with('status', 'Kategori berhasil dihapus.');
    }
}
