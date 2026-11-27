<x-panel-admin judul="Kelola Kategori" label="Taksonomi Lowongan" aktif="kategori" deskripsi="Tambah atau hapus kategori pekerjaan yang digunakan pada katalog lowongan.">
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('kategori.store') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:flex-row sm:items-end" data-kirim>
        @csrf
        <div class="flex-1">
            <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="nama-kategori">Nama kategori baru</label>
            <input id="nama-kategori" name="nama" placeholder="Contoh: Teknik Sipil & Arsitektur" required>
        </div>
        <button class="tombol-utama shrink-0" type="submit" data-teks-proses="Menyimpan…">Tambah Kategori</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <ul class="divide-y divide-slate-100">
            @forelse ($daftarKategori as $kategori)
                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 hover:bg-slate-50">
                    <div>
                        <strong class="block text-sm font-semibold text-slate-900">{{ $kategori->nama }}</strong>
                        <p class="text-xs text-slate-500">{{ $kategori->lowongans_count }} lowongan terkait</p>
                    </div>
                    @if ($kategori->lowongans_count === 0)
                        <form method="POST" action="{{ route('kategori.destroy', $kategori) }}" data-kirim>
                            @csrf
                            @method('DELETE')
                            <button class="tombol-bahaya rounded-lg px-3 py-1.5 text-sm font-semibold" type="submit">Hapus</button>
                        </form>
                    @else
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-500">Digunakan</span>
                    @endif
                </li>
            @empty
                <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada kategori. Tambahkan kategori pertama di atas.</li>
            @endforelse
        </ul>
        <div class="border-t border-slate-100 px-5 py-3">{{ $daftarKategori->links() }}</div>
    </div>
</x-panel-admin>
