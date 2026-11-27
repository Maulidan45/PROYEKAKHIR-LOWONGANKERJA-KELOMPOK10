<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifikasiPerusahaanNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function verifikasiIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $statusFilter = $permintaan->query('status');
        $kueri = User::query()
            ->where('role', 'perusahaan')
            ->withCount('lowongans')
            ->latest('id');

        if (is_string($statusFilter) && in_array($statusFilter, User::STATUS_VERIFIKASI, true)) {
            $kueri->where('status_verifikasi', $statusFilter);
        }

        return view('admin.verifikasi', [
            'daftarPerusahaan' => $kueri->paginate(12)->withQueryString(),
            'statusAktif' => $statusFilter,
            'ringkasanVerifikasi' => User::query()
                ->where('role', 'perusahaan')
                ->selectRaw('status_verifikasi, count(*) as total')
                ->groupBy('status_verifikasi')
                ->pluck('total', 'status_verifikasi'),
        ]);
    }

    public function verifikasiUpdate(Request $permintaan, User $perusahaan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin' && $perusahaan->role === 'perusahaan', 403);

        $valid = $permintaan->validate([
            'status_verifikasi' => ['required', Rule::in(['Terverifikasi', 'Ditolak', 'Menunggu Verifikasi'])],
            'catatan_verifikasi' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
        ]);

        $perusahaan->status_verifikasi = $valid['status_verifikasi'];
        $perusahaan->catatan_verifikasi = $valid['catatan_verifikasi'] ?? null;
        $perusahaan->diverifikasi_pada = $valid['status_verifikasi'] === 'Terverifikasi' ? now() : null;
        $perusahaan->save();

        $perusahaan->notify(new VerifikasiPerusahaanNotification($perusahaan));

        return back()->with('status', "Status verifikasi {$perusahaan->name} diperbarui menjadi {$perusahaan->status_verifikasi}.");
    }

    public function unduhLegalitas(Request $permintaan, User $perusahaan): StreamedResponse
    {
        $pengguna = $permintaan->user();
        $berhak = $pengguna->role === 'admin' || $pengguna->id === $perusahaan->id;
        abort_unless($berhak && $perusahaan->role === 'perusahaan', 403);
        abort_unless(
            is_string($perusahaan->dokumen_legalitas_path)
            && $perusahaan->dokumen_legalitas_path !== ''
            && Storage::disk('local')->exists($perusahaan->dokumen_legalitas_path),
            404
        );

        $ekstensi = pathinfo($perusahaan->dokumen_legalitas_path, PATHINFO_EXTENSION) ?: 'pdf';

        return Storage::disk('local')->download(
            $perusahaan->dokumen_legalitas_path,
            'Legalitas-'.Str::slug($perusahaan->name).'.'.$ekstensi
        );
    }

    public function penggunaIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $peranFilter = $permintaan->query('role');
        $cari = trim((string) $permintaan->query('kata_kunci', ''));

        $kueri = User::query()->latest('id');

        if (is_string($peranFilter) && array_key_exists($peranFilter, User::JENIS_AKUN)) {
            $kueri->where('role', $peranFilter);
        }

        if ($cari !== '') {
            $pola = '%'.mb_strtolower($cari).'%';
            $kueri->where(function ($q) use ($pola): void {
                $q->whereRaw('lower(name) like ?', [$pola])
                    ->orWhereRaw('lower(email) like ?', [$pola]);
            });
        }

        return view('admin.pengguna', [
            'daftarPengguna' => $kueri->paginate(15)->withQueryString(),
            'peranAktif' => $peranFilter,
            'cari' => $cari,
            'jenisAkunTersedia' => User::JENIS_AKUN,
        ]);
    }
}
