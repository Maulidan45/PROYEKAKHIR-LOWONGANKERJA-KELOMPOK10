<x-panel-admin judul="Kelola Kategori" aktif="kategori">
    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-800 backdrop-blur-md" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('kategori.store') }}" class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:flex-row sm:items-end sm:p-6" data-kirim>
        @csrf
        <div class="flex-1">
            <label class="mb-1.5 block text-sm font-bold text-slate-700" for="nama-kategori">Nama kategori baru</label>
            <input id="nama-kategori" name="nama" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Contoh: Teknik Sipil & Arsitektur" required>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Tambah Kategori</button>
    </form>

    <div class="overflow-hidden rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl">
        <ul class="divide-y divide-slate-200/50">
            @forelse ($daftarKategori as $kategori)
                <li class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 transition hover:bg-white/70">
                    <div>
                        <strong class="block text-sm font-bold text-slate-900">{{ $kategori->nama }}</strong>
                        <p class="text-xs text-slate-500">{{ $kategori->lowongans_count }} lowongan terkait</p>
                    </div>
                    @if ($kategori->lowongans_count === 0)
                        <form method="POST" action="{{ route('kategori.destroy', $kategori) }}" data-kirim>
                            @csrf
                            @method('DELETE')
                            <button class="rounded-full bg-rose-50 px-4 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100" type="submit">Hapus</button>
                        </form>
                    @else
                        <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">Digunakan</span>
                    @endif
                </li>
            @empty
                <li class="px-6 py-10 text-center text-sm text-slate-500">Belum ada kategori. Tambahkan kategori pertama di atas.</li>
            @endforelse
        </ul>
        <div class="border-t border-slate-200/60 px-6 py-3.5">{{ $daftarKategori->links() }}</div>
    </div>
</x-panel-admin>
