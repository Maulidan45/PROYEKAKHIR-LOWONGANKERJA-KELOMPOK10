<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $permintaan): View
    {
        $daftarNotifikasi = $permintaan->user()->notifications()->latest()->paginate(15);

        return view('notifikasi.index', [
            'daftarNotifikasi' => $daftarNotifikasi,
            'jumlahBelumDibaca' => $permintaan->user()->unreadNotifications()->count(),
        ]);
    }

    public function bacaSemua(Request $permintaan): RedirectResponse
    {
        $permintaan->user()->unreadNotifications->markAsRead();
        Cache::forget("user:{$permintaan->user()->id}:unread_notif");

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function bacaSatu(Request $permintaan, string $id): RedirectResponse
    {
        $notifikasi = $permintaan->user()->notifications()->where('id', $id)->firstOrFail();
        $notifikasi->markAsRead();
        Cache::forget("user:{$permintaan->user()->id}:unread_notif");

        $urlTujuan = $notifikasi->data['url'] ?? null;
        if (is_string($urlTujuan) && $urlTujuan !== '') {
            return redirect()->to($urlTujuan);
        }

        return back();
    }
}
