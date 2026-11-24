<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiLowonganRequest;
use App\Http\Requests\SimpanLowonganRequest;
use App\Http\Resources\LowonganResource;
use App\Models\Lowongan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LowonganController extends Controller
{
    public function index(Request $permintaan): AnonymousResourceCollection
    {
        $penyaring = $permintaan->validate([
            'kata_kunci' => ['nullable', 'string', 'max:150'],
            'kategori' => ['nullable', 'integer', 'exists:kategoris,id'],
            'tipe' => ['nullable', 'in:Penuh waktu,Paruh waktu,Magang,Jarak jauh'],
            'lokasi' => ['nullable', 'string', 'max:100'],
        ]);

        $kueri = Lowongan::terbuka()->with(['kategori:id,nama', 'perusahaan:id,name'])->latest();
        $kueri->when($penyaring['kata_kunci'] ?? null, function (Builder $kueri, string $cari): void {
            $nilaiCari = '%'.mb_strtolower($cari).'%';
            $kueri->where(fn (Builder $bagian) => $bagian
                ->whereRaw('lower(judul) like ?', [$nilaiCari])
                ->orWhereRaw('lower(lokasi) like ?', [$nilaiCari])
                ->orWhereHas('perusahaan', fn (Builder $perusahaan) => $perusahaan->whereRaw('lower(name) like ?', [$nilaiCari])));
        });
        $kueri->when($penyaring['kategori'] ?? null, fn (Builder $kueri, int $kategori) => $kueri->where('kategori_id', $kategori));
        $kueri->when($penyaring['tipe'] ?? null, fn (Builder $kueri, string $tipe) => $kueri->where('tipe_kerja', $tipe));
        $kueri->when($penyaring['lokasi'] ?? null, fn (Builder $kueri, string $lokasi) => $kueri->where('lokasi', $lokasi));

        return LowonganResource::collection($kueri->paginate(12)->withQueryString());
    }

    public function store(SimpanLowonganRequest $permintaan): JsonResponse
    {
        $this->authorize('create', Lowongan::class);
        $data = $permintaan->validated();
        $data['aktif'] = $permintaan->boolean('aktif');
        $lowongan = $permintaan->user()->lowongans()->create($data);

        return LowonganResource::make($lowongan->load(['kategori:id,nama', 'perusahaan:id,name']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Lowongan $lowongan): LowonganResource
    {
        abort_unless($lowongan->aktif && $lowongan->batas_lamaran->gte(today()), 404);

        return LowonganResource::make($lowongan->load(['kategori:id,nama', 'perusahaan:id,name']));
    }

    public function update(PerbaruiLowonganRequest $permintaan, Lowongan $lowongan): LowonganResource
    {
        $data = $permintaan->validated();
        $data['aktif'] = $permintaan->boolean('aktif');
        $lowongan->update($data);

        return LowonganResource::make($lowongan->load(['kategori:id,nama', 'perusahaan:id,name']));
    }

    public function destroy(Lowongan $lowongan): JsonResponse
    {
        $this->authorize('delete', $lowongan);
        $lowongan->delete();

        return response()->json(status: 204);
    }
}
