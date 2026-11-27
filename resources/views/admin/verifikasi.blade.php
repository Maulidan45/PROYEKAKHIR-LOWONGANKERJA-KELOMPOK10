<x-panel-admin judul="Verifikasi Perusahaan" label="Moderasi & Legalitas" aktif="verifikasi" deskripsi="Periksa profil dan dokumen legalitas perusahaan sebelum menyetujui publikasi lowongan.">
    <div class="mb-5 flex flex-wrap gap-2">
        <a @class(['rounded-lg border px-3.5 py-2 text-sm font-medium transition', 'border-blue-600 bg-blue-600 text-white' => ! $statusAktif, 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => $statusAktif]) href="{{ route('admin.verifikasi.index') }}">Semua</a>
        @foreach (App\Models\User::STATUS_VERIFIKASI as $st)
            <a @class(['rounded-lg border px-3.5 py-2 text-sm font-medium transition', 'border-blue-600 bg-blue-600 text-white' => $statusAktif === $st, 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => $statusAktif !== $st]) href="{{ route('admin.verifikasi.index', ['status' => $st]) }}">
                {{ $st }} ({{ $ringkasanVerifikasi[$st] ?? 0 }})
            </a>
        @endforeach
    </div>

    <div class="grid gap-4">
        @forelse ($daftarPerusahaan as $perusahaan)
            @php($profil = $perusahaan->profil_pendaftaran ?? [])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
                <div class="flex flex-col justify-between gap-4 border-b border-slate-100 pb-4 sm:flex-row sm:items-start">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900">{{ $perusahaan->name }}</h2>
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-amber-50 text-amber-800' => $perusahaan->status_verifikasi === 'Menunggu Verifikasi',
                                'bg-emerald-50 text-emerald-800' => $perusahaan->status_verifikasi === 'Terverifikasi',
                                'bg-red-50 text-red-800' => $perusahaan->status_verifikasi === 'Ditolak',
                            ])>{{ $perusahaan->status_verifikasi }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $perusahaan->email }} &middot; {{ $profil['kota_perusahaan'] ?? '-' }} &middot; {{ $profil['bidang_usaha'] ?? '-' }}</p>
                        @if (! empty($profil['nama_penanggung_jawab']))
                            <p class="mt-1 text-xs text-slate-500">PIC: {{ $profil['nama_penanggung_jawab'] }} ({{ $profil['jabatan_penanggung_jawab'] ?? '-' }}) &middot; Telp: {{ $profil['telepon'] ?? '-' }}</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        @if ($perusahaan->dokumen_legalitas_path)
                            <a class="tombol-kedua" href="{{ route('perusahaan.legalitas', $perusahaan) }}">Unduh Dokumen Legalitas</a>
                        @else
                            <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500">Belum unggah dokumen</span>
                        @endif
                    </div>
                </div>

                @if (! empty($profil['deskripsi_perusahaan']))
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $profil['deskripsi_perusahaan'] }}</p>
                @endif

                <form class="mt-4 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-end" method="POST" action="{{ route('admin.verifikasi.update', $perusahaan) }}" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="sm:w-52">
                        <label class="mb-1 block text-xs font-semibold text-slate-700" for="status-verif-{{ $perusahaan->id }}">Keputusan Verifikasi</label>
                        <select id="status-verif-{{ $perusahaan->id }}" name="status_verifikasi">
                            @foreach (App\Models\User::STATUS_VERIFIKASI as $opsiStatus)
                                <option value="{{ $opsiStatus }}" @selected($perusahaan->status_verifikasi === $opsiStatus)>{{ $opsiStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-700" for="catatan-verif-{{ $perusahaan->id }}">Catatan untuk perusahaan (opsional)</label>
                        <input id="catatan-verif-{{ $perusahaan->id }}" name="catatan_verifikasi" value="{{ $perusahaan->catatan_verifikasi }}" placeholder="Contoh: NIB valid / Mohon unggah ulang NIB yang jelas...">
                    </div>
                    <button class="tombol-utama shrink-0" type="submit" data-teks-proses="Menyimpan…">Simpan Verifikasi</button>
                </form>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Tidak ada data perusahaan pada filter ini.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $daftarPerusahaan->links() }}</div>
</x-panel-admin>
