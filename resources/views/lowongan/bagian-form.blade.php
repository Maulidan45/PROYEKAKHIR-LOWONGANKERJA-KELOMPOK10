<label class="grid gap-1.5 text-sm font-medium text-slate-700 sm:col-span-2">
    Kategori
    <select name="kategori_id" required>
        @foreach ($daftarKategori as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $lowongan->kategori_id) == $kategori->id)>{{ $kategori->nama }}</option>
        @endforeach
    </select>
</label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700 sm:col-span-2">Judul posisi<input name="judul" value="{{ old('judul', $lowongan->judul) }}" required></label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700">Lokasi<input name="lokasi" value="{{ old('lokasi', $lowongan->lokasi) }}" required></label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700">Tipe kerja
    <select name="tipe_kerja" required>
        @foreach (['Penuh waktu', 'Paruh waktu', 'Magang', 'Jarak jauh'] as $tipe)
            <option @selected(old('tipe_kerja', $lowongan->tipe_kerja) === $tipe)>{{ $tipe }}</option>
        @endforeach
    </select>
</label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700">Gaji minimal Rp<input type="number" min="0" step="100000" name="gaji_min" value="{{ old('gaji_min', $lowongan->gaji_min) }}" required></label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700">Gaji maksimal Rp<input type="number" min="0" step="100000" name="gaji_max" value="{{ old('gaji_max', $lowongan->gaji_max) }}" required></label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700">Batas lamaran<input type="date" name="batas_lamaran" value="{{ old('batas_lamaran', $lowongan->batas_lamaran?->format('Y-m-d')) }}" required></label>
<label class="grid gap-1.5 text-sm font-medium text-slate-700 sm:col-span-2">Deskripsi<textarea name="deskripsi" rows="6" required>{{ old('deskripsi', $lowongan->deskripsi) }}</textarea></label>
<label class="flex min-h-11 items-center gap-3 text-sm font-medium text-slate-700 sm:col-span-2"><input class="size-4 accent-blue-700" type="checkbox" name="aktif" value="1" @checked(old('aktif', $lowongan->exists ? $lowongan->aktif : true))> Lowongan aktif</label>