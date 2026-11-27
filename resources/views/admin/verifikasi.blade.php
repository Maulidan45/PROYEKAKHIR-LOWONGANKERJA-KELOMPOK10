<x-panel-admin judul="Verifikasi Perusahaan" aktif="verifikasi">
    <div class="flex flex-wrap gap-2">
        <a @class(['rounded-full border-[1.5px] px-4 py-2 text-xs font-semibold transition sm:text-sm', 'border-[#007AFF] bg-[#007AFF] text-white shadow-md shadow-[#007AFF]/20' => ! $statusAktif, 'border-slate-200/90 bg-white/85 text-slate-700 backdrop-blur-md hover:border-[#007AFF]/40 hover:bg-[#E0F2FE]/70 hover:text-[#007AFF]' => $statusAktif]) href="{{ route('admin.verifikasi.index') }}">Semua</a>
        @foreach (App\Models\User::STATUS_VERIFIKASI as $st)
            <a @class(['rounded-full border-[1.5px] px-4 py-2 text-xs font-semibold transition sm:text-sm', 'border-[#007AFF] bg-[#007AFF] text-white shadow-md shadow-[#007AFF]/20' => $statusAktif === $st, 'border-slate-200/90 bg-white/85 text-slate-700 backdrop-blur-md hover:border-[#007AFF]/40 hover:bg-[#E0F2FE]/70 hover:text-[#007AFF]' => $statusAktif !== $st]) href="{{ route('admin.verifikasi.index', ['status' => $st]) }}">
                {{ $st }} ({{ $ringkasanVerifikasi[$st] ?? 0 }})
            </a>
        @endforeach
    </div>

    <div class="grid gap-4 sm:gap-5">
        @forelse ($daftarPerusahaan as $perusahaan)
            @php($profil = $perusahaan->profil_pendaftaran ?? [])
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px] sm:p-7">
                <div class="flex flex-col justify-between gap-4 border-b border-slate-200/60 pb-5 sm:flex-row sm:items-start">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="text-lg font-bold text-slate-900">{{ $perusahaan->name }}</h2>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-bold',
                                'bg-amber-100 text-amber-800' => $perusahaan->status_verifikasi === 'Menunggu Verifikasi',
                                'bg-emerald-100 text-emerald-800' => $perusahaan->status_verifikasi === 'Terverifikasi',
                                'bg-rose-100 text-rose-800' => $perusahaan->status_verifikasi === 'Ditolak',
                            ])>{{ $perusahaan->status_verifikasi }}</span>
                        </div>
                        <p class="mt-1.5 text-sm text-slate-600">{{ $perusahaan->email }} &middot; {{ $profil['kota_perusahaan'] ?? '-' }} &middot; {{ $profil['bidang_usaha'] ?? '-' }}</p>
                        @if (! empty($profil['nama_penanggung_jawab']))
                            <p class="mt-1 text-xs text-slate-500">PIC: {{ $profil['nama_penanggung_jawab'] }} ({{ $profil['jabatan_penanggung_jawab'] ?? '-' }}) &middot; Telp: {{ $profil['telepon'] ?? '-' }}</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        @if ($perusahaan->dokumen_legalitas_path)
                            <a class="inline-flex min-h-10 items-center rounded-full border border-[#007AFF]/30 bg-[#E0F2FE] px-4 text-xs font-bold text-[#007AFF] transition hover:bg-sky-200/70" href="{{ route('perusahaan.legalitas', $perusahaan) }}">Unduh Dokumen Legalitas</a>
                        @else
                            <span class="rounded-full border border-slate-200 bg-slate-100/80 px-3.5 py-1.5 text-xs font-medium text-slate-500">Belum unggah dokumen</span>
                        @endif
                    </div>
                </div>

                @if (! empty($profil['deskripsi_perusahaan']))
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $profil['deskripsi_perusahaan'] }}</p>
                @endif

                <form class="mt-5 flex flex-col gap-3 rounded-2xl border border-slate-200/70 bg-white/70 p-4 sm:flex-row sm:items-end" method="POST" action="{{ route('admin.verifikasi.update', $perusahaan) }}" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="sm:w-52">
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="status-verif-{{ $perusahaan->id }}">Keputusan Verifikasi</label>
                        <select id="status-verif-{{ $perusahaan->id }}" name="status_verifikasi" class="w-full rounded-2xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                            @foreach (App\Models\User::STATUS_VERIFIKASI as $opsiStatus)
                                <option value="{{ $opsiStatus }}" @selected($perusahaan->status_verifikasi === $opsiStatus)>{{ $opsiStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="catatan-verif-{{ $perusahaan->id }}">Catatan untuk perusahaan (opsional)</label>
                        <input id="catatan-verif-{{ $perusahaan->id }}" name="catatan_verifikasi" value="{{ $perusahaan->catatan_verifikasi }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none" placeholder="Contoh: NIB valid / Mohon unggah ulang NIB yang jelas...">
                    </div>
                    <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Simpan Verifikasi</button>
                </form>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white/60 p-12 text-center text-sm text-slate-500 backdrop-blur-md">Tidak ada data perusahaan pada filter ini.</div>
        @endforelse
    </div>

    <div>{{ $daftarPerusahaan->links() }}</div>
</x-panel-admin>
