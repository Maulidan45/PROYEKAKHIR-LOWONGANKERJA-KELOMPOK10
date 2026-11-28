<x-panel-perusahaan judul="Profil Perusahaan" aktif="akun">
    @php($profil = $pengguna->profil_pendaftaran ?? [])
    @php($syaratProfilLengkap = filled($profil['bentuk_badan_usaha'] ?? null) && filled($profil['tahun_berdiri'] ?? null) && filled($profil['telepon'] ?? null) && filled($profil['email_hrd'] ?? null) && filled($profil['bidang_usaha'] ?? null) && filled($profil['jumlah_karyawan'] ?? null) && filled($profil['provinsi_perusahaan'] ?? null) && filled($profil['kota_perusahaan'] ?? null) && filled($profil['kode_pos'] ?? null) && filled($profil['alamat_perusahaan'] ?? null) && mb_strlen((string) ($profil['deskripsi_perusahaan'] ?? '')) >= 30 && mb_strlen((string) ($profil['visi_misi'] ?? '')) >= 20 && filled($profil['nama_penanggung_jawab'] ?? null) && filled($profil['jabatan_penanggung_jawab'] ?? null))
    @php($syaratLogoAda = filled($pengguna->foto_profil_path))
    @php($syaratLegalitasAda = filled($pengguna->dokumen_legalitas_path))
    @php($bagianValidasi = $errors->hasAny(['verifikasi', 'nib_npwp', 'pernyataan_kebenaran']) ? 'verifikasi-akun' : ($errors->hasAny(['name', 'bentuk_badan_usaha', 'tahun_berdiri', 'nib_npwp', 'telepon', 'email_hrd', 'bidang_usaha', 'jumlah_karyawan', 'provinsi_perusahaan', 'kota_perusahaan', 'kode_pos', 'alamat_perusahaan', 'deskripsi_perusahaan', 'visi_misi', 'keunggulan_perusahaan', 'situs_perusahaan', 'linkedin_url', 'nama_penanggung_jawab', 'jabatan_penanggung_jawab', 'foto_profil', 'dokumen_legalitas']) ? 'profil-perusahaan' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null))))
    @php($bagianAwal = $bagianValidasi ?? 'profil-perusahaan')

    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-4 text-sm text-rose-900 shadow-xs backdrop-blur-xl" role="alert" tabindex="-1" data-kesalahan-formulir>
            <h2 class="font-bold">Perubahan belum disimpan.</h2>
            <p class="mt-0.5 text-xs">Periksa kembali isian yang ditandai di bawah.</p>
        </div>
    @endif

    <!-- Hero Profile Banner -->
    <section class="overflow-hidden rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
        <div class="h-20 bg-gradient-to-r from-[#007AFF] via-[#0A84FF] to-sky-400 sm:h-24"></div>
        <div class="px-5 pb-5 sm:px-7 sm:pb-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    @if ($pengguna->foto_profil_path)
                        <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="-mt-10 size-18 shrink-0 rounded-2xl border-4 border-white bg-white object-cover shadow-md sm:-mt-12 sm:size-20">
                    @else
                        <span class="-mt-10 grid size-18 shrink-0 place-items-center rounded-2xl border-4 border-white bg-[#E0F2FE] text-xl font-extrabold text-[#007AFF] shadow-md sm:-mt-12 sm:size-20">{{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 pt-2">
                        <h1 class="truncate !text-base font-extrabold !leading-tight text-slate-900 sm:!text-lg">{{ $pengguna->name }}</h1>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $pengguna->email }} &middot; {{ $profil['bidang_usaha'] ?? 'Perusahaan Mitra' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-bold',
                        'bg-emerald-100 text-emerald-800' => $pengguna->status_verifikasi === 'Terverifikasi',
                        'bg-amber-100 text-amber-800' => $pengguna->status_verifikasi === 'Menunggu Verifikasi',
                        'bg-rose-100 text-rose-800' => $pengguna->status_verifikasi === 'Ditolak',
                        'bg-slate-100 text-slate-700' => $pengguna->status_verifikasi === 'Belum Diajukan',
                    ])>
                        <span @class([
                            'size-2 rounded-full',
                            'bg-emerald-500' => $pengguna->status_verifikasi === 'Terverifikasi',
                            'bg-amber-500' => $pengguna->status_verifikasi === 'Menunggu Verifikasi',
                            'bg-rose-500' => $pengguna->status_verifikasi === 'Ditolak',
                            'bg-slate-400' => $pengguna->status_verifikasi === 'Belum Diajukan',
                        ])></span>
                        {{ $pengguna->status_verifikasi }}
                    </span>
                </div>
            </div>
            @if ($pengguna->catatan_verifikasi)
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-xs text-amber-900">
                    <strong>Catatan Admin:</strong> {{ $pengguna->catatan_verifikasi }}
                </div>
            @endif
        </div>
    </section>

    <!-- 2-Column Bento Settings -->
    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-12">
        <!-- Left Navigation Pills -->
        <nav class="flex gap-1.5 overflow-x-auto rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-2 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:sticky lg:top-24 lg:col-span-3 lg:flex-col lg:p-3" aria-label="Bagian pengaturan akun" data-navigasi-akun>
            <p class="hidden px-3 py-1.5 text-[11px] font-extrabold tracking-wider text-slate-400 uppercase lg:block">Pengaturan</p>
            <a href="#profil-perusahaan" @if ($bagianAwal === 'profil-perusahaan') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/></svg>
                <span>Profil Perusahaan</span>
            </a>
            <a href="#verifikasi-akun" @if ($bagianAwal === 'verifikasi-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                <span>Verifikasi</span>
            </a>
            <a href="#email-akun" @if ($bagianValidasi === 'email-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                <span>Email Akun</span>
            </a>
            <a href="#keamanan-akun" @if ($bagianValidasi === 'keamanan-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>
                <span>Kata Sandi</span>
            </a>
            <a href="#keluar-akun" class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50 aria-[current=location]:bg-rose-600 aria-[current=location]:!text-white">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                <span>Keluar</span>
            </a>
        </nav>

        <!-- Right Content Panels -->
        <div class="min-w-0 space-y-5 lg:col-span-9">
            <section id="profil-perusahaan" data-panel-akun @if ($bagianAwal !== 'profil-perusahaan') hidden @endif>
                <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data" class="space-y-5" data-kirim>
                    @csrf
                    @method('PATCH')

                    <!-- Card 1: Identitas & Logo -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-[#E0F2FE] text-[#007AFF]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Identitas &amp; Logo Perusahaan</h2>
                                <p class="text-[11px] text-slate-500">Tampil di halaman publik dan kartu lowongan Anda</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <span class="mb-1.5 block text-xs font-bold text-slate-700">Logo / Foto Profil Perusahaan (JPG, PNG, WEBP, maks 2 MB)</span>
                                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-3.5">
                                    @if ($pengguna->foto_profil_path)
                                        <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="size-14 rounded-2xl border border-slate-200 object-cover">
                                    @else
                                        <span class="grid size-14 place-items-center rounded-2xl bg-[#E0F2FE] text-lg font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}</span>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3">
                                        <input id="foto_profil_pt" name="foto_profil" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only" onchange="document.getElementById('nama-file-foto-pt').textContent = this.files[0]?.name || 'Belum ada file dipilih';">
                                        <label for="foto_profil_pt" class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                            Upload Foto
                                        </label>
                                        <span id="nama-file-foto-pt" class="text-xs font-medium text-slate-500">Belum ada file dipilih</span>
                                    </div>
                                </div>
                                @error('foto_profil')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="name">Nama Perusahaan</label>
                                <input id="name" name="name" value="{{ old('name', $pengguna->name) }}" maxlength="255" required>
                                @error('name')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="bentuk_badan_usaha">Bentuk Badan Usaha</label>
                                <select id="bentuk_badan_usaha" name="bentuk_badan_usaha" required>
                                    <option value="">Pilih badan usaha</option>
                                    @foreach (['PT', 'CV', 'Firma', 'Koperasi', 'Yayasan', 'UMKM / Perseorangan'] as $bentuk)
                                        <option value="{{ $bentuk }}" @selected(old('bentuk_badan_usaha', $profil['bentuk_badan_usaha'] ?? '') === $bentuk)>{{ $bentuk }}</option>
                                    @endforeach
                                </select>
                                @error('bentuk_badan_usaha')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="tahun_berdiri">Tahun Berdiri</label>
                                <input id="tahun_berdiri" name="tahun_berdiri" type="number" min="1900" max="{{ date('Y') }}" value="{{ old('tahun_berdiri', $profil['tahun_berdiri'] ?? '') }}" placeholder="Contoh: 2018" required>
                                @error('tahun_berdiri')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="nib_npwp_profil">Nomor NIB / NPWP Perusahaan</label>
                                <input id="nib_npwp_profil" name="nib_npwp" value="{{ old('nib_npwp', $profil['nib_npwp'] ?? '') }}" minlength="10" maxlength="50" placeholder="Contoh: 1234567890123">
                                @error('nib_npwp')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="telepon">Nomor Telepon Kantor</label>
                                <input id="telepon" name="telepon" type="tel" value="{{ old('telepon', $profil['telepon'] ?? '') }}" maxlength="25" autocomplete="tel" required>
                                @error('telepon')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="email_hrd">Email HRD / Rekrutmen Resmi</label>
                                <input id="email_hrd" name="email_hrd" type="email" value="{{ old('email_hrd', $profil['email_hrd'] ?? '') }}" maxlength="255" placeholder="hrd@perusahaan.co.id" required>
                                @error('email_hrd')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="bidang_usaha">Bidang Usaha</label>
                                <input id="bidang_usaha" name="bidang_usaha" value="{{ old('bidang_usaha', $profil['bidang_usaha'] ?? '') }}" maxlength="100" required>
                                @error('bidang_usaha')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="jumlah_karyawan">Jumlah Karyawan</label>
                                <select id="jumlah_karyawan" name="jumlah_karyawan" required>
                                    @foreach (['1–10', '11–50', '51–200', '201–500', '501+'] as $jumlah)
                                        <option value="{{ $jumlah }}" @selected(old('jumlah_karyawan', $profil['jumlah_karyawan'] ?? '') === $jumlah)>{{ $jumlah }}</option>
                                    @endforeach
                                </select>
                                @error('jumlah_karyawan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="situs_perusahaan">Situs Perusahaan (opsional)</label>
                                <input id="situs_perusahaan" name="situs_perusahaan" type="url" value="{{ old('situs_perusahaan', $profil['situs_perusahaan'] ?? '') }}" maxlength="255" placeholder="https://perusahaan.co.id">
                                @error('situs_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="linkedin_url">LinkedIn / Media Sosial Resmi (opsional)</label>
                                <input id="linkedin_url" name="linkedin_url" type="url" value="{{ old('linkedin_url', $profil['linkedin_url'] ?? '') }}" maxlength="255" placeholder="https://linkedin.com/company/...">
                                @error('linkedin_url')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Lokasi & Deskripsi -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-[#E0F2FE] text-[#007AFF]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Lokasi, Visi Misi &amp; Budaya Kerja</h2>
                                <p class="text-[11px] text-slate-500">Membantu kandidat mengenal budaya dan alamat lengkap kantor</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="provinsi_perusahaan">Provinsi</label>
                                <input id="provinsi_perusahaan" name="provinsi_perusahaan" value="{{ old('provinsi_perusahaan', $profil['provinsi_perusahaan'] ?? '') }}" maxlength="100" placeholder="Contoh: DKI Jakarta" required>
                                @error('provinsi_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="kota_perusahaan">Kota / Kabupaten</label>
                                <input id="kota_perusahaan" name="kota_perusahaan" value="{{ old('kota_perusahaan', $profil['kota_perusahaan'] ?? '') }}" maxlength="100" placeholder="Contoh: Jakarta Selatan" required>
                                @error('kota_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="kode_pos">Kode Pos</label>
                                <input id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $profil['kode_pos'] ?? '') }}" maxlength="5" pattern="\d{5}" placeholder="12190" required>
                                @error('kode_pos')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="alamat_perusahaan">Alamat Lengkap Kantor</label>
                                <textarea id="alamat_perusahaan" name="alamat_perusahaan" rows="2" maxlength="500" required>{{ old('alamat_perusahaan', $profil['alamat_perusahaan'] ?? '') }}</textarea>
                                @error('alamat_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="deskripsi_perusahaan">Deskripsi Perusahaan (min. 30 karakter)</label>
                                <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="3" minlength="30" maxlength="2000" required>{{ old('deskripsi_perusahaan', $profil['deskripsi_perusahaan'] ?? '') }}</textarea>
                                @error('deskripsi_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="visi_misi">Visi &amp; Misi Perusahaan (min. 20 karakter)</label>
                                <textarea id="visi_misi" name="visi_misi" rows="2" minlength="20" maxlength="1500" placeholder="Tuliskan visi dan misi utama perusahaan Anda…" required>{{ old('visi_misi', $profil['visi_misi'] ?? '') }}</textarea>
                                @error('visi_misi')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="keunggulan_perusahaan">Benefit &amp; Budaya Kerja (opsional)</label>
                                <input id="keunggulan_perusahaan" name="keunggulan_perusahaan" value="{{ old('keunggulan_perusahaan', $profil['keunggulan_perusahaan'] ?? '') }}" maxlength="1000" placeholder="Contoh: BPJS Kesehatan & Ketenagakerjaan, Hybrid Working, Bonus Tahunan">
                                @error('keunggulan_perusahaan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Penanggung Jawab & Legalitas -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Penanggung Jawab &amp; Legalitas</h2>
                                <p class="text-[11px] text-slate-500">Digunakan admin untuk proses verifikasi akun perusahaan</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="nama_penanggung_jawab">Nama Penanggung Jawab</label>
                                <input id="nama_penanggung_jawab" name="nama_penanggung_jawab" value="{{ old('nama_penanggung_jawab', $profil['nama_penanggung_jawab'] ?? '') }}" maxlength="255" required>
                                @error('nama_penanggung_jawab')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="jabatan_penanggung_jawab">Jabatan Penanggung Jawab</label>
                                <input id="jabatan_penanggung_jawab" name="jabatan_penanggung_jawab" value="{{ old('jabatan_penanggung_jawab', $profil['jabatan_penanggung_jawab'] ?? '') }}" maxlength="100" required>
                                @error('jabatan_penanggung_jawab')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end border-t border-slate-200/70 pt-4">
                            <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-7 text-sm font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Simpan Profil Perusahaan</button>
                        </div>
                    </div>
                </form>
            </section>

            <section id="verifikasi-akun" data-panel-akun @if ($bagianAwal !== 'verifikasi-akun') hidden @endif class="space-y-5">
                <!-- Status Verifikasi Card -->
                <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/70 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Pengajuan Verifikasi Perusahaan</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Perusahaan wajib lolos verifikasi admin sebelum dapat mempublikasikan lowongan kerja.</p>
                        </div>
                        <span @class([
                            'rounded-full px-3.5 py-1.5 text-xs font-bold',
                            'bg-emerald-100 text-emerald-800' => $pengguna->status_verifikasi === 'Terverifikasi',
                            'bg-amber-100 text-amber-800' => $pengguna->status_verifikasi === 'Menunggu Verifikasi',
                            'bg-rose-100 text-rose-800' => $pengguna->status_verifikasi === 'Ditolak',
                            'bg-slate-100 text-slate-700' => $pengguna->status_verifikasi === 'Belum Diajukan',
                        ])>{{ $pengguna->status_verifikasi }}</span>
                    </div>

                    @error('verifikasi')
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">{{ $message }}</div>
                    @enderror

                    @if ($pengguna->status_verifikasi === 'Terverifikasi')
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-xs text-emerald-900">
                            <strong class="block font-bold">Perusahaan Anda telah terverifikasi!</strong>
                            <p class="mt-1">Diverifikasi pada {{ $pengguna->diverifikasi_pada?->format('d M Y H:i') ?? '-' }}. Anda sudah dapat memasang lowongan kerja dan menggunakan fitur Lokerku Ads.</p>
                        </div>
                    @elseif ($pengguna->status_verifikasi === 'Menunggu Verifikasi')
                        <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900">
                            <strong class="block font-bold">Pengajuan sedang ditinjau oleh Admin</strong>
                            <p class="mt-1">Data profil, NIB/NPWP ({{ $profil['nib_npwp'] ?? '-' }}), dan dokumen legalitas Anda sudah masuk antrean verifikasi Admin.</p>
                        </div>
                    @endif

                    @if ($pengguna->catatan_verifikasi)
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-900">
                            <strong class="block font-bold">Catatan Penolakan / Admin:</strong>
                            <p class="mt-1">{{ $pengguna->catatan_verifikasi }}</p>
                        </div>
                    @endif

                    <!-- Checklist Persyaratan Ketat -->
                    <div class="mt-5">
                        <h3 class="text-xs font-extrabold tracking-wide text-slate-700 uppercase">Checklist Persyaratan Ketat</h3>
                        <ul class="mt-3 grid gap-2.5 sm:grid-cols-2">
                            <li @class(['flex items-center gap-2.5 rounded-2xl border p-3 text-xs font-semibold', 'border-emerald-200 bg-emerald-50/70 text-emerald-800' => $syaratProfilLengkap, 'border-amber-200 bg-amber-50/70 text-amber-800' => ! $syaratProfilLengkap])>
                                <span class="grid size-6 shrink-0 place-items-center rounded-full {{ $syaratProfilLengkap ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-white' }}">{{ $syaratProfilLengkap ? '✓' : '!' }}</span>
                                Profil, lokasi, deskripsi (&ge;30 karakter) &amp; PIC lengkap
                            </li>
                            <li @class(['flex items-center gap-2.5 rounded-2xl border p-3 text-xs font-semibold', 'border-emerald-200 bg-emerald-50/70 text-emerald-800' => $syaratLogoAda, 'border-amber-200 bg-amber-50/70 text-amber-800' => ! $syaratLogoAda])>
                                <span class="grid size-6 shrink-0 place-items-center rounded-full {{ $syaratLogoAda ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-white' }}">{{ $syaratLogoAda ? '✓' : '!' }}</span>
                                Logo / Foto Profil perusahaan sudah diunggah
                            </li>
                            <li @class(['flex items-center gap-2.5 rounded-2xl border p-3 text-xs font-semibold', 'border-emerald-200 bg-emerald-50/70 text-emerald-800' => $syaratLegalitasAda, 'border-amber-200 bg-amber-50/70 text-amber-800' => ! $syaratLegalitasAda])>
                                <span class="grid size-6 shrink-0 place-items-center rounded-full {{ $syaratLegalitasAda ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-white' }}">{{ $syaratLegalitasAda ? '✓' : '!' }}</span>
                                Dokumen legalitas usaha (NIB / SIUP / Akta)
                            </li>
                            <li @class(['flex items-center gap-2.5 rounded-2xl border p-3 text-xs font-semibold', 'border-emerald-200 bg-emerald-50/70 text-emerald-800' => filled($profil['nib_npwp'] ?? null), 'border-slate-200 bg-slate-50 text-slate-600' => empty($profil['nib_npwp'] ?? null)])>
                                <span class="grid size-6 shrink-0 place-items-center rounded-full {{ filled($profil['nib_npwp'] ?? null) ? 'bg-emerald-500 text-white' : 'bg-slate-400 text-white' }}">{{ filled($profil['nib_npwp'] ?? null) ? '✓' : '•' }}</span>
                                Nomor NIB / NPWP Perusahaan valid
                            </li>
                        </ul>
                    </div>

                    @if ($pengguna->status_verifikasi !== 'Terverifikasi')
                        <form method="POST" action="{{ route('akun.verifikasi.ajukan') }}" enctype="multipart/form-data" class="mt-6 space-y-4 border-t border-slate-200/70 pt-5" data-kirim>
                            @csrf
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="nib_npwp">Nomor NIB / NPWP Perusahaan (Wajib)</label>
                                <input id="nib_npwp" name="nib_npwp" value="{{ old('nib_npwp', $profil['nib_npwp'] ?? '') }}" minlength="10" maxlength="50" required placeholder="Contoh: 1234567890123 atau 01.234.567.8-901.000">
                                @error('nib_npwp')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <span class="mb-1.5 block text-xs font-bold text-slate-700">Dokumen Legalitas (NIB / SIUP / Akta PDF/JPG/PNG, maks 5 MB)</span>
                                <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-3.5">
                                    <input id="dokumen_legalitas_verif" name="dokumen_legalitas" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only" onchange="document.getElementById('nama-file-legalitas-verif').textContent = this.files[0]?.name || 'Belum ada file dipilih';" @if (! $syaratLegalitasAda) required @endif>
                                    <label for="dokumen_legalitas_verif" class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0062CC]">
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                        Upload Legalitas
                                    </label>
                                    <span id="nama-file-legalitas-verif" class="text-xs font-medium text-slate-500">{{ $syaratLegalitasAda ? 'Dokumen sudah tersimpan (opsional ganti)' : 'Belum ada file dipilih' }}</span>
                                </div>
                                @error('dokumen_legalitas')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <label class="flex items-start gap-2.5 rounded-2xl border border-slate-200/80 bg-slate-50/80 p-3.5 text-xs text-slate-700">
                                <input type="checkbox" name="pernyataan_kebenaran" value="1" class="mt-0.5 size-4 shrink-0 accent-[#007AFF]" required @checked(old('pernyataan_kebenaran'))>
                                <span>Saya menyatakan bahwa seluruh data perusahaan, NIB/NPWP, dan dokumen legalitas yang dikirim adalah benar, sah secara hukum, dan bersedia ditolak atau dibanned apabila ditemukan pemalsuan.</span>
                            </label>
                            @error('pernyataan_kebenaran')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-7 text-sm font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" data-teks-proses="Mengajukan…">
                                    {{ $pengguna->status_verifikasi === 'Ditolak' ? 'Ajukan Ulang Verifikasi' : 'Ajukan Verifikasi ke Admin' }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>

            <section id="email-akun" data-panel-akun @if ($bagianAwal !== 'email-akun') hidden @endif class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <h2 class="text-base font-bold text-slate-900">Email Akun</h2>
                <p class="mt-0.5 text-xs text-slate-500">Alamat email untuk masuk dan menerima informasi akun.</p>
                <form method="POST" action="{{ route('akun.email.update') }}" class="mt-5 grid max-w-lg gap-4" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" maxlength="255" autocomplete="username" required>
                        @error('email')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="current_password_email">Kata sandi saat ini</label>
                        <input id="current_password_email" name="current_password_email" type="password" autocomplete="current-password" maxlength="72" required>
                        @error('current_password_email')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Simpan Email</button>
                    </div>
                </form>
            </section>

            <section id="keamanan-akun" data-panel-akun @if ($bagianAwal !== 'keamanan-akun') hidden @endif class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <h2 class="text-base font-bold text-slate-900">Kata Sandi</h2>
                <p class="mt-0.5 text-xs text-slate-500">Gunakan kata sandi yang kuat (minimal 12 karakter).</p>
                <form method="POST" action="{{ route('akun.kata_sandi.update') }}" class="mt-5 grid max-w-lg gap-4" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="current_password_password">Kata sandi saat ini</label>
                        <input id="current_password_password" name="current_password_password" type="password" autocomplete="current-password" maxlength="72" required>
                        @error('current_password_password')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="password">Kata sandi baru</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                        @error('password')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="password_confirmation">Ulangi kata sandi baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                    </div>
                    <div>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Ubah Kata Sandi</button>
                    </div>
                </form>
            </section>

            <section id="keluar-akun" data-panel-akun @if ($bagianAwal !== 'keluar-akun') hidden @endif class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Keluar dari Akun</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Akhiri sesi akun ini pada perangkat yang sedang digunakan.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" data-kirim>
                    @csrf
                    <button class="rounded-full bg-rose-600 px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700" type="submit" data-teks-proses="Keluar…">Keluar</button>
                </form>
            </section>
        </div>
    </div>
</x-panel-perusahaan>
