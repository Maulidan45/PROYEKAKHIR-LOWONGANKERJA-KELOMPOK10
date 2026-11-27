@php($perusahaan = auth()->user()->role === 'perusahaan')
@if ($perusahaan)
<x-panel-perusahaan :judul="'Pelamar · ' . $lowongan->judul" aktif="pelamar">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Pelamar · {{ $lowongan->judul }}</h1>
        <a class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('perusahaan.pelamar.index') }}">Semua Pelamar</a>
    </div>

            @php($jumlahPerStatus = $lowongan->lamarans->countBy('status'))
            <div class="mb-5 flex flex-wrap items-center gap-2" aria-label="Ringkasan status lamaran">
                <span class="inline-flex min-h-8 items-center rounded-full bg-blue-700 px-3 text-xs font-semibold text-white tabular-nums">{{ $lowongan->lamarans->count() }} lamaran</span>
                @foreach (App\Models\Lamaran::STATUS as $status)
                    <span class="inline-flex min-h-8 items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs text-slate-600">{{ $status }} <strong class="font-bold text-slate-900 tabular-nums">{{ $jumlahPerStatus->get($status, 0) }}</strong></span>
                @endforeach
            </div>

            <section class="grid gap-4" aria-label="Daftar kandidat">
                @forelse ($lowongan->lamarans as $lamaran)
                    @php($snap = $lamaran->data_pelamar ?? [])
                    @php($namaPelamar = $snap['nama_lengkap'] ?? $lamaran->pelamar->name)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($namaPelamar, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <strong class="text-base font-bold text-slate-900">{{ $namaPelamar }}</strong>
                                        <x-status-lamaran :status="$lamaran->status" />
                                    </div>
                                    <p class="mt-0.5 text-sm text-slate-600">{{ $snap['email'] ?? $lamaran->pelamar->email }} &middot; {{ $snap['telepon'] ?? '-' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $snap['pendidikan_terakhir'] ?? '-' }} &middot; {{ $snap['domisili'] ?? '-' }} &middot; Melamar {{ $lamaran->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <a class="tombol-utama" href="{{ route('lamaran.show', $lamaran) }}">Lihat Profil &amp; Dokumen</a>
                                @if ($lamaran->cv_path)
                                    <a class="tombol-kedua" href="{{ route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']) }}">Lihat CV</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <form class="flex flex-wrap items-center gap-2" method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                @csrf
                                @method('PATCH')
                                <select class="w-48" name="status" aria-label="Status lamaran {{ $namaPelamar }}">
                                    @foreach (App\Models\Lamaran::STATUS as $status)
                                        <option value="{{ $status }}" @selected($lamaran->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="tombol-kedua" type="submit" data-teks-proses="Menyimpan…">Simpan Tahapan</button>
                            </form>
                            <div class="flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Diterima">
                                    <button class="inline-flex min-h-10 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 text-sm font-semibold text-emerald-800 hover:bg-emerald-100" type="submit">Terima</button>
                                </form>
                                <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button class="inline-flex min-h-10 items-center rounded-lg border border-red-200 bg-red-50 px-3.5 text-sm font-semibold text-red-800 hover:bg-red-100" type="submit">Tolak</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <h2 class="text-lg font-semibold text-slate-900">Belum ada pelamar pada lowongan ini.</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Sebarkan lowongan atau periksa batas lamarannya agar kandidat bisa melamar.</p>
                        <a class="tombol-kedua mt-4" href="{{ route('lowongan.edit', $lowongan) }}">Ubah lowongan</a>
                    </div>
                @endforelse
        </section>
    </x-panel-perusahaan>
@else
<x-panel-admin :judul="'Pelamar: ' . $lowongan->judul" label="Pantauan Pelamar" aktif="lowongan" :deskripsi="$lowongan->perusahaan->name . ' · ' . $lowongan->lokasi">
    <x-slot:aksi>
        <a class="tombol-kedua" href="{{ route('lowongan.index') }}">Kembali ke Lowongan</a>
    </x-slot:aksi>
    <div class="grid gap-3">
        @forelse ($lowongan->lamarans as $lamaran)
            <article class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                <div>
                    <strong class="block text-sm font-semibold text-slate-900">{{ $lamaran->pelamar->name }}</strong>
                    <p class="text-xs text-slate-500">Melamar {{ $lamaran->created_at->format('d M Y') }}</p>
                </div>
                <x-status-lamaran :status="$lamaran->status" />
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Belum ada pelamar pada lowongan ini.</div>
        @endforelse
    </div>
</x-panel-admin>
@endif
