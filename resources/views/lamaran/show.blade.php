<x-dynamic-component :component="$adalahPerusahaan ? 'panel-perusahaan' : 'panel-user'" :judul="'Detail Lamaran - ' . $lamaran->lowongan->judul" :aktif="$adalahPerusahaan ? 'pelamar' : 'lamaran'">
    @php($dataPelamar = $lamaran->data_pelamar ?? [])
    <div>
        <nav class="mb-5 flex items-center justify-between gap-3 text-sm">
            <a class="font-medium text-blue-700 hover:underline" href="{{ $adalahPerusahaan ? route('lowongan.pelamar', $lamaran->lowongan) : route('lamaran.index') }}">
                &larr; Kembali ke {{ $adalahPerusahaan ? 'daftar pelamar' : 'riwayat lamaran' }}
            </a>
            <x-status-lamaran :status="$lamaran->status" />
        </nav>

        <header class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
            <p class="text-xs font-bold tracking-wider text-blue-700 uppercase">{{ $lamaran->lowongan->perusahaan->name }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $lamaran->lowongan->judul }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $lamaran->lowongan->lokasi }} &middot; {{ $lamaran->lowongan->kategori->nama }} &middot; Diajukan pada {{ $lamaran->created_at->format('d M Y, H:i') }}</p>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <!-- Tracking Perkembangan Seleksi -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs" aria-labelledby="judul-tracking">
                    <h2 class="text-lg font-bold text-slate-900" id="judul-tracking">Tracking Tahapan Seleksi</h2>
                    <p class="mt-1 text-xs text-slate-500">Riwayat perubahan status lamaran secara kronologis.</p>

                    <ol class="mt-5 space-y-4 border-l-2 border-blue-100 pl-5">
                        @forelse ($lamaran->riwayats as $riwayat)
                            <li class="relative">
                                <span class="absolute -left-[27px] top-1 size-3 rounded-full border-2 border-white bg-blue-600 ring-2 ring-blue-100" aria-hidden="true"></span>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="text-sm font-semibold text-slate-900">{{ $riwayat->status_baru }}</strong>
                                    <span class="text-xs text-slate-500">{{ $riwayat->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                @if ($riwayat->catatan)
                                    <p class="mt-1 text-sm text-slate-600">{{ $riwayat->catatan }}</p>
                                @endif
                                @if ($riwayat->pengubah)
                                    <p class="mt-0.5 text-xs text-slate-400">Diperbarui oleh: {{ $riwayat->pengubah->name }}</p>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Status saat ini: <strong>{{ $lamaran->status }}</strong> (diajukan {{ $lamaran->created_at->format('d M Y') }}).</li>
                        @endforelse
                    </ol>
                </section>

                <!-- Snapshot Data Pelamar -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs" aria-labelledby="judul-data-pelamar">
                    <h2 class="text-lg font-bold text-slate-900" id="judul-data-pelamar">Data Pelamar Saat Pengajuan</h2>
                    <p class="mt-1 text-xs text-slate-500">Snapshot data pelamar yang tersimpan pada lamaran ini.</p>

                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                        @php
                            $bidangTampil = [
                                'Nama lengkap' => $dataPelamar['nama_lengkap'] ?? $lamaran->pelamar->name,
                                'Email' => $dataPelamar['email'] ?? $lamaran->pelamar->email,
                                'Nomor telepon' => $dataPelamar['telepon'] ?? '-',
                                'Jenis kelamin' => $dataPelamar['jenis_kelamin'] ?? '-',
                                'Tempat, tanggal lahir' => trim(($dataPelamar['tempat_lahir'] ?? '-') . ', ' . ($dataPelamar['tanggal_lahir'] ?? '-'), ', '),
                                'Domisili' => $dataPelamar['domisili'] ?? '-',
                                'Pendidikan terakhir' => $dataPelamar['pendidikan_terakhir'] ?? '-',
                                'Keahlian' => $dataPelamar['keahlian'] ?? '-',
                            ];
                        @endphp
                        @foreach ($bidangTampil as $label => $nilai)
                            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <dt class="text-xs font-medium text-slate-500">{{ $label }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900 break-words">{{ $nilai }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if (! empty($dataPelamar['pengalaman_kerja']))
                        <div class="mt-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-medium text-slate-500">Pengalaman kerja</p>
                            <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $dataPelamar['pengalaman_kerja'] }}</p>
                        </div>
                    @endif

                    @if ($lamaran->catatan)
                        <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                            <p class="text-xs font-medium text-blue-700">Pesan / Catatan Pelamar</p>
                            <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $lamaran->catatan }}</p>
                        </div>
                    @endif
                </section>

                <!-- Dokumen Persyaratan -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs" aria-labelledby="judul-dokumen">
                    <h2 class="text-lg font-bold text-slate-900" id="judul-dokumen">Dokumen Persyaratan</h2>
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($lamaran->daftarDokumen() as $jenis => $info)
                            <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                                <span class="text-sm font-medium text-slate-800">{{ $info['label'] }}</span>
                                @if ($info['path'])
                                    <a class="tombol-kedua" href="{{ route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => $jenis]) }}">Unduh Dokumen</a>
                                @else
                                    <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <!-- Sidebar: Jadwal Wawancara & Aksi Perusahaan -->
            <aside class="space-y-6">
                @if ($lamaran->jadwalWawancaras->isNotEmpty())
                    <section class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5">
                        <h2 class="text-base font-bold text-amber-950">Jadwal Wawancara</h2>
                        <div class="mt-3 space-y-3">
                            @foreach ($lamaran->jadwalWawancaras as $jadwal)
                                <div class="rounded-xl border border-amber-200 bg-white p-4 text-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <strong class="text-slate-900">{{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jam }}</strong>
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">{{ $jadwal->status }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-medium text-blue-700">{{ $jadwal->tipe }}</p>
                                    <p class="mt-1 text-xs text-slate-700 break-all">{{ $jadwal->lokasi_atau_tautan }}</p>
                                    @if ($jadwal->catatan)
                                        <p class="mt-2 text-xs text-slate-500">{{ $jadwal->catatan }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($adalahPerusahaan)
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <h2 class="text-base font-bold text-slate-900">Proses Seleksi Pelamar</h2>
                        <form class="mt-4 space-y-3" method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700" for="status-seleksi">Tahapan Seleksi</label>
                                <select id="status-seleksi" name="status">
                                    @foreach (App\Models\Lamaran::STATUS as $st)
                                        <option value="{{ $st }}" @selected($lamaran->status === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700" for="catatan-seleksi">Catatan perubahan (opsional)</label>
                                <textarea id="catatan-seleksi" name="catatan" rows="2" placeholder="Catatan untuk pelamar..."></textarea>
                            </div>
                            <button class="tombol-utama w-full" type="submit" data-teks-proses="Menyimpan…">Simpan Tahapan</button>
                        </form>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <h2 class="text-base font-bold text-slate-900">Jadwalkan Wawancara</h2>
                        <form class="mt-4 space-y-3" method="POST" action="{{ route('wawancara.store', $lamaran) }}" data-kirim>
                            @csrf
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700" for="tanggal-wawancara">Tanggal</label>
                                    <input id="tanggal-wawancara" type="date" name="tanggal" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700" for="jam-wawancara">Jam (JJ:MM)</label>
                                    <input id="jam-wawancara" type="time" name="jam" required>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700" for="tipe-wawancara">Tipe</label>
                                <select id="tipe-wawancara" name="tipe">
                                    @foreach (App\Models\JadwalWawancara::TIPE as $tp)
                                        <option value="{{ $tp }}">{{ $tp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700" for="lokasi-wawancara">Lokasi atau Tautan Meeting</label>
                                <input id="lokasi-wawancara" name="lokasi_atau_tautan" placeholder="https://meet.google.com/... atau Alamat Kantor" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700" for="catatan-wawancara">Pesan untuk pelamar</label>
                                <textarea id="catatan-wawancara" name="catatan" rows="2" placeholder="Siapkan portofolio atau dokumen asli..."></textarea>
                            </div>
                            <button class="tombol-kedua w-full" type="submit" data-teks-proses="Menjadwalkan…">Buat Jadwal Wawancara</button>
                        </form>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</x-dynamic-component>
