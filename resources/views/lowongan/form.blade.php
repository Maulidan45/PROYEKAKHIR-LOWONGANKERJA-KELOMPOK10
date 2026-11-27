@if (auth()->user()->role === 'perusahaan')
<x-panel-perusahaan :judul="$aksi" aktif="lowongan">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $aksi }}</h1>
        <a class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lowongan.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $lowongan->exists ? route('lowongan.update', $lowongan) : route('lowongan.store') }}" class="grid gap-5 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:grid-cols-2 sm:p-7" data-kirim>
        @csrf
        @if ($lowongan->exists)
            @method('PUT')
        @endif
        @include('lowongan.bagian-form')
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 sm:col-span-2" role="alert">{{ $errors->first() }}</div>
        @endif
        <div class="sm:col-span-2">
            <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">{{ $aksi }}</button>
        </div>
    </form>
</x-panel-perusahaan>
@else
<x-layout :judul="'Form lowongan | Lokerku'">
    <div class="pembungkus halaman-aplikasi">
        <x-menu-peran :jenis-akun="auth()->user()->role" />
        <section class="isi-aplikasi">
            <header class="kepala-halaman-aplikasi"><h1>{{ $aksi }}</h1></header>
            <form method="POST" action="{{ $lowongan->exists ? route('lowongan.update', $lowongan) : route('lowongan.store') }}" class="formulir-autentikasi">
                @csrf
                @if ($lowongan->exists)
                    @method('PUT')
                @endif
                @include('lowongan.bagian-form')
                @if ($errors->any())
                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">{{ $errors->first() }}</div>
                @endif
                <button class="tombol">{{ $aksi }}</button>
            </form>
        </section>
    </div>
</x-layout>
@endif
