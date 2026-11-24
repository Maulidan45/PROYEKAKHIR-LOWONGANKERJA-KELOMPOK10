@props(['lowongan'])

<article {{ $attributes->merge(['class' => 'kartu-lowongan']) }}>
    <div class="identitas-lowongan">
        <span class="inisial-perusahaan" aria-hidden="true">{{ mb_substr(str_replace('PT ', '', $lowongan['perusahaan']), 0, 1) }}</span>
        <div>
            <p class="perusahaan-lowongan">{{ $lowongan['perusahaan'] }}</p>
            <h3>{{ $lowongan['judul'] }}</h3>
            <p class="lokasi-lowongan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $lowongan['lokasi'] }}
            </p>
        </div>
    </div>
    <ul class="rincian-lowongan" aria-label="Informasi posisi">
        <li>{{ $lowongan['tipe'] }}</li>
        <li>{{ $lowongan['kategori'] }}</li>
    </ul>
    <div class="bagian-bawah-lowongan">
        <p class="gaji-lowongan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>
            {{ $lowongan['gaji'] }}<span class="periode-gaji"> / bulan</span>
        </p>
        <span class="kode-lowongan">{{ $lowongan['kode'] }}</span>
    </div>
</article>
