@php($peranDetail = auth()->user()?->role)
@php($komponenDetail = match ($peranDetail) {
    'user' => 'panel-user',
    'perusahaan' => 'panel-perusahaan',
    'admin' => 'panel-admin',
    default => 'layout',
})
<x-dynamic-component :component="$komponenDetail" :judul="$komponenDetail === 'layout' ? $lowongan->judul . ' - ' . $lowongan->perusahaan->name . ' | Lokerku' : $lowongan->judul" :aktif="$peranDetail === 'user' ? 'katalog' : 'lowongan'">
    @php($profilPt = $lowongan->perusahaan->profil_pendaftaran ?? [])
    <section @class(['detail-lowongan-halaman' => true, 'pembungkus' => $komponenDetail === 'layout']) aria-labelledby="judul-lowongan">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a class="inline-flex items-center gap-2 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 px-4 py-2 text-xs font-bold text-slate-700 shadow-xs backdrop-blur-md transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ auth()->check() ? route('katalog') : route('home') }}">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
                Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="tata-letak-detail">
            <div class="kolom-utama-detail">
                <article class="kartu-detail-lowongan">
                    <header class="kepala-detail-lowongan">
                        <div class="baris-atas-detail">
                            <div>
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    @if ($lowongan->adalahEksternal())
                                        <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-semibold text-indigo-700">Lowongan Eksternal · Sumber Remotive</span>
                                    @else
                                        <span class="rounded-full bg-blue-50 px-3 py-0.5 text-xs font-semibold text-blue-700">Lowongan Internal Lokerku</span>
                                    @endif
                                </div>
                                <h1 id="judul-lowongan">{{ $lowongan->judul }}</h1>
                                <p class="subjudul-detail-pt">
                                    <a href="#profil-perusahaan">{{ $lowongan->perusahaan->name }}</a>
                                    <span aria-hidden="true">&middot;</span>
                                    <span>Diposting {{ $lowongan->created_at->locale('id')->diffForHumans() }}</span>
                                </p>
                            </div>
                            <div class="aksi-atas-detail">
                                @auth
                                    @if (auth()->user()->role === 'user')
                                        <form method="POST" action="{{ route('lowongan.simpan', $lowongan) }}">
                                            @csrf
                                            <button class="tombol tombol-sekunder" type="submit">
                                                {{ $sudahDisimpan ? 'Tersimpan' : 'Simpan' }}
                                            </button>
                                        </form>
                                        @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                                            <a class="tombol tombol-utama" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Penyedia</a>
                                        @elseif ($sudahMelamar && $lamaranPengguna)
                                            <a class="tombol tombol-sekunder" href="{{ route('lamaran.show', $lamaranPengguna) }}">Lihat Status Lamaran</a>
                                        @elseif ($lowongan->masihMenerimaLamaran())
                                            <button type="button" class="tombol tombol-utama" onclick="const m = document.getElementById('modal-lamar-cepat'); m.classList.remove('hidden'); m.classList.add('flex');">Lamar Sekarang</button>
                                        @endif
                                    @endif
                                @else
                                    @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                                        <a class="tombol tombol-utama" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Penyedia</a>
                                    @else
                                        <a class="tombol tombol-utama" href="{{ route('login') }}">Lamar Sekarang</a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="kisi-meta-detail">
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <div>
                                    <span class="label-detail">Lokasi</span>
                                    <strong class="nilai-detail">{{ $lowongan->lokasi }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16"/></svg>
                                <div>
                                    <span class="label-detail">Tipe Pekerjaan</span>
                                    <strong class="nilai-detail">{{ $lowongan->tipe_kerja }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                                <div>
                                    <span class="label-detail">Kategori / Fungsi</span>
                                    <strong class="nilai-detail">{{ $lowongan->kategori->nama }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>
                                <div>
                                    <span class="label-detail">Estimasi Gaji</span>
                                    <strong class="nilai-detail">
                                        @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }} – {{ number_format($lowongan->gaji_max, 0, ',', '.') }} / bulan
                                        @else
                                            Tidak dicantumkan oleh sumber
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                                <div>
                                    <span class="label-detail">Batas Lamaran</span>
                                    <strong class="nilai-detail">{{ $lowongan->batas_lamaran?->format('d M Y') ?? 'Mengikuti situs sumber' }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                                <div>
                                    <span class="label-detail">Status Lowongan</span>
                                    <strong class="nilai-detail">{{ $lowongan->statusLowongan() }}</strong>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="deskripsi-detail-lowongan">
                        <h2>Deskripsi Pekerjaan</h2>
                        <p>{!! nl2br(e($lowongan->deskripsi)) !!}</p>
                    </div>

                    <footer class="aksi-detail-lowongan">
                        @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-5">
                                <h2 class="text-base font-bold text-indigo-950">Lowongan Eksternal (Remotive)</h2>
                                <p class="mt-1 text-sm text-indigo-800">Proses pengajuan lamaran untuk posisi ini dilakukan langsung pada situs penyedia.</p>
                                <a class="tombol-utama mt-4" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Penyedia</a>
                            </div>
                        @elseif (! $lowongan->masihMenerimaLamaran())
                            <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600">Lowongan ini sudah {{ mb_strtolower($lowongan->statusLowongan()) }} dan tidak menerima lamaran baru.</p>
                        @else
                            @auth
                                @if (auth()->user()->role === 'user')
                                    @if ($sudahMelamar && $lamaranPengguna)
                                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                            <div>
                                                <p class="text-sm font-semibold text-emerald-900">Anda sudah melamar posisi ini.</p>
                                                <p class="text-xs text-emerald-700">Status saat ini: {{ $lamaranPengguna->status }}</p>
                                            </div>
                                            <a class="tombol-utama" href="{{ route('lamaran.show', $lamaranPengguna) }}">Lihat Detail &amp; Tracking</a>
                                        </div>
                                    @else
                                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-blue-200 bg-blue-50/70 p-5">
                                            <div>
                                                <h2 class="text-base font-bold text-slate-900">Tertarik dengan posisi ini?</h2>
                                                <p class="mt-1 text-sm text-slate-600">Validasi data dan berkas lamaran Anda untuk langsung melamar.</p>
                                            </div>
                                            <button type="button" class="tombol-utama shrink-0" onclick="const m = document.getElementById('modal-lamar-cepat'); m.classList.remove('hidden'); m.classList.add('flex');">Lamar Sekarang</button>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-blue-200 bg-blue-50/70 p-5">
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900">Siap melamar posisi ini?</h2>
                                        <p class="mt-1 text-sm text-slate-600">Masuk terlebih dahulu dengan akun pencari kerja untuk mengisi formulir lamaran.</p>
                                    </div>
                                    <a class="tombol-utama shrink-0" href="{{ route('login') }}">Masuk untuk Melamar</a>
                                </div>
                            @endauth
                        @endif
                    </footer>
                </article>
            </div>

            <aside class="kolom-profil-pt" id="profil-perusahaan" aria-label="Profil perusahaan">
                <section class="kartu-profil-pt">
                    <div class="kepala-profil-pt">
                        @if ($lowongan->perusahaan->foto_profil_path)
                            <img src="{{ asset('storage/' . $lowongan->perusahaan->foto_profil_path) }}" alt="{{ $lowongan->perusahaan->name }}" class="size-14 shrink-0 rounded-2xl border border-slate-200 object-cover">
                        @else
                            <span class="inisial-perusahaan inisial-pt-besar" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                        @endif
                        <div>
                            <h2>{{ $lowongan->perusahaan->name }}</h2>
                            <p class="status-pt-aktif">{{ $lowongan->perusahaan->sudahTerverifikasi() ? 'Perusahaan Terverifikasi' : 'Perusahaan Terdaftar' }}</p>
                        </div>
                    </div>

                    <ul class="daftar-info-pt">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $profilPt['kota_perusahaan'] ?? $lowongan->lokasi }}</span>
                        </li>
                        @if (! empty($profilPt['bidang_usaha']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5h6v2"/></svg>
                                <span>{{ $profilPt['bidang_usaha'] }}</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['jumlah_karyawan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5M16 11a2.5 2.5 0 1 0 0-5M17 15c2.2.3 3.6 1.8 4 5"/></svg>
                                <span>{{ $profilPt['jumlah_karyawan'] }} Karyawan</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['alamat_perusahaan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                                <span>{{ $profilPt['alamat_perusahaan'] }}</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['situs_perusahaan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
                                <a href="{{ $profilPt['situs_perusahaan'] }}" target="_blank" rel="noopener noreferrer">{{ $profilPt['situs_perusahaan'] }}</a>
                            </li>
                        @endif
                    </ul>

                    <div class="tentang-pt">
                        <h3>Tentang Perusahaan</h3>
                        <p>{{ $profilPt['deskripsi_perusahaan'] ?? $lowongan->perusahaan->name . ' membuka kesempatan karier melalui Lokerku.' }}</p>
                    </div>
                </section>

                @if ($lowonganLainPerusahaan->isNotEmpty())
                    <section class="kartu-profil-pt kartu-lowongan-lain-pt">
                        <h3>Lowongan Lainnya di {{ $lowongan->perusahaan->name }}</h3>
                        <ul class="daftar-lowongan-lain-pt">
                            @foreach ($lowonganLainPerusahaan as $lain)
                                <li>
                                    <a href="{{ route('lowongan.show', $lain) }}">
                                        <strong>{{ $lain->judul }}</strong>
                                        <span>{{ $lain->lokasi }} &middot; {{ $lain->tipe_kerja }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>
    </section>

    @if (auth()->check() && auth()->user()->role === 'user' && ! $lowongan->adalahEksternal() && $lowongan->masihMenerimaLamaran() && ! $sudahMelamar)
        @php($pelamar = auth()->user())
        @php($profilPelamar = $pelamar->profil_pendaftaran ?? [])
        @php($dataPribadiLengkap = filled($profilPelamar['telepon'] ?? null) && filled($profilPelamar['tempat_lahir'] ?? null) && filled($profilPelamar['tanggal_lahir'] ?? null) && filled($profilPelamar['jenis_kelamin'] ?? null) && filled($profilPelamar['domisili'] ?? null) && filled($profilPelamar['pendidikan_terakhir'] ?? null) && filled($profilPelamar['pengalaman_kerja'] ?? null) && filled($profilPelamar['keahlian'] ?? null))
        @php($berkasProfilLengkap = $dataPribadiLengkap && ! empty($profilPelamar['cv_default_path']) && ! empty($profilPelamar['surat_lamaran_default_path']) && ! empty($profilPelamar['ijazah_default_path']))

            <!-- Pop-up Modal Berkas / Validasi Cek Berkas -->
            <div id="modal-lamar-cepat" class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="judul-modal-lamar" onclick="if (event.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }">
                <div class="max-h-[88vh] w-full max-w-2xl overflow-y-auto rounded-[28px] border-[1.5px] border-slate-200/90 bg-white/95 p-6 shadow-2xl backdrop-blur-2xl sm:p-7">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-200/70 pb-4">
                        <div>
                            <span class="inline-flex rounded-full bg-[#E0F2FE] px-3 py-1 text-[11px] font-bold text-[#007AFF]">
                                {{ $berkasProfilLengkap ? 'Validasi Cek Berkas' : 'Lengkapi Berkas Lamaran' }}
                            </span>
                            <h2 id="judul-modal-lamar" class="mt-2 text-lg font-extrabold text-slate-900">{{ $lowongan->judul }}</h2>
                            <p class="text-xs text-slate-500">{{ $lowongan->perusahaan->name }} &middot; {{ $lowongan->lokasi }}</p>
                        </div>
                        <button type="button" onclick="const m = document.getElementById('modal-lamar-cepat'); m.classList.add('hidden'); m.classList.remove('flex');" class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-100" aria-label="Tutup">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-900">
                            <ul class="list-disc pl-4 space-y-0.5">
                                @foreach ($errors->all() as $galat)
                                    <li>{{ $galat }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lamaran.store', $lowongan) }}" enctype="multipart/form-data" class="mt-5 space-y-4" data-kirim>
                        @csrf

                        @if ($berkasProfilLengkap)
                            <!-- Profile & Files Already Complete: Validasi Cek Berkas -->
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 text-xs text-emerald-950">
                                <p class="font-bold">Data &amp; berkas profil Anda sudah lengkap!</p>
                                <p class="mt-1 text-emerald-800">Silakan cek ringkasan di bawah ini sebelum mengirim lamaran:</p>
                                <ul class="mt-3 grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                                    <li>&#10003; Nama: <strong>{{ $pelamar->name }}</strong></li>
                                    <li>&#10003; Telepon: <strong>{{ $profilPelamar['telepon'] }}</strong></li>
                                    <li>&#10003; Domisili: <strong>{{ $profilPelamar['domisili'] }}</strong></li>
                                    <li>&#10003; Pendidikan: <strong>{{ $profilPelamar['pendidikan_terakhir'] }}</strong></li>
                                    <li>&#10003; CV Utama: <strong>Tersedia (PDF)</strong></li>
                                    <li>&#10003; Surat Lamaran &amp; Ijazah: <strong>Tersedia</strong></li>
                                </ul>
                            </div>

                            <input type="hidden" name="nama_lengkap" value="{{ $pelamar->name }}">
                            <input type="hidden" name="email" value="{{ $pelamar->email }}">
                            <input type="hidden" name="telepon" value="{{ $profilPelamar['telepon'] }}">
                            <input type="hidden" name="tempat_lahir" value="{{ $profilPelamar['tempat_lahir'] }}">
                            <input type="hidden" name="tanggal_lahir" value="{{ $profilPelamar['tanggal_lahir'] }}">
                            <input type="hidden" name="jenis_kelamin" value="{{ $profilPelamar['jenis_kelamin'] }}">
                            <input type="hidden" name="domisili" value="{{ $profilPelamar['domisili'] }}">
                            <input type="hidden" name="pendidikan_terakhir" value="{{ $profilPelamar['pendidikan_terakhir'] }}">
                            <input type="hidden" name="pengalaman_kerja" value="{{ $profilPelamar['pengalaman_kerja'] }}">
                            <input type="hidden" name="keahlian" value="{{ $profilPelamar['keahlian'] }}">

                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700" for="catatan-cepat">Pesan untuk perusahaan (opsional)</label>
                                <textarea id="catatan-cepat" name="catatan" rows="2" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs text-slate-800 focus:border-[#007AFF] focus:outline-none" placeholder="Sampaikan ketertarikan Anda pada posisi ini...">{{ old('catatan') }}</textarea>
                            </div>
                        @else
                            <!-- Incomplete Profile or Documents: Upload Berkas in Modal -->
                            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-900">
                                Lengkapi data &amp; unggah berkas lamaran di bawah ini (atau lengkapi sekali di <a href="{{ route('akun.edit') }}" class="font-bold underline">Profil</a> agar lamaran berikutnya cukup cek berkas).
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap</label>
                                    <input name="nama_lengkap" value="{{ old('nama_lengkap', $pelamar->name) }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Email</label>
                                    <input name="email" type="email" value="{{ old('email', $pelamar->email) }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Nomor Telepon</label>
                                    <input name="telepon" value="{{ old('telepon', $profilPelamar['telepon'] ?? '') }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Domisili</label>
                                    <input name="domisili" value="{{ old('domisili', $profilPelamar['domisili'] ?? '') }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Tempat Lahir</label>
                                    <input name="tempat_lahir" value="{{ old('tempat_lahir', $profilPelamar['tempat_lahir'] ?? '') }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Tanggal Lahir</label>
                                    <input name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $profilPelamar['tanggal_lahir'] ?? '') }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" required>
                                        <option value="">Pilih</option>
                                        @foreach (['Laki-laki', 'Perempuan'] as $jk)
                                            <option value="{{ $jk }}" @selected(old('jenis_kelamin', $profilPelamar['jenis_kelamin'] ?? '') === $jk)>{{ $jk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Pendidikan Terakhir</label>
                                    <select name="pendidikan_terakhir" required>
                                        <option value="">Pilih</option>
                                        @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $pend)
                                            <option value="{{ $pend }}" @selected(old('pendidikan_terakhir', $profilPelamar['pendidikan_terakhir'] ?? '') === $pend)>{{ $pend }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Keahlian</label>
                                    <input name="keahlian" value="{{ old('keahlian', $profilPelamar['keahlian'] ?? '') }}" placeholder="Contoh: Laravel, PHP, Excel" required>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Pengalaman Kerja</label>
                                    <textarea name="pengalaman_kerja" rows="2" required>{{ old('pengalaman_kerja', $profilPelamar['pengalaman_kerja'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">CV (PDF) @if(!empty($profilPelamar['cv_default_path'])) <span class="text-emerald-600">(Opsional, ada di profil)</span> @endif</label>
                                    <input name="cv" type="file" accept=".pdf" @if(empty($profilPelamar['cv_default_path'])) required @endif>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Surat Lamaran (PDF) @if(!empty($profilPelamar['surat_lamaran_default_path'])) <span class="text-emerald-600">(Opsional, ada di profil)</span> @endif</label>
                                    <input name="surat_lamaran" type="file" accept=".pdf" @if(empty($profilPelamar['surat_lamaran_default_path'])) required @endif>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">Ijazah Terakhir (PDF/JPG/PNG) @if(!empty($profilPelamar['ijazah_default_path'])) <span class="text-emerald-600">(Opsional, ada di profil)</span> @endif</label>
                                    <input name="ijazah" type="file" accept=".pdf,.jpg,.jpeg,.png" @if(empty($profilPelamar['ijazah_default_path'])) required @endif>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200/70 pt-4">
                            <button type="button" onclick="const m = document.getElementById('modal-lamar-cepat'); m.classList.add('hidden'); m.classList.remove('flex');" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="rounded-full bg-[#007AFF] px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" data-teks-proses="Mengirim…">
                                {{ $berkasProfilLengkap ? 'Konfirmasi & Kirim Lamaran' : 'Kirim Lamaran' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    @endif
</x-dynamic-component>
