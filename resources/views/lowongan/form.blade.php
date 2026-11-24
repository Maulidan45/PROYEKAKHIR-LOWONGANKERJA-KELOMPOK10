<x-layout :judul="'Form lowongan | Lokerku'">
    <div @class(['mx-auto w-[min(52rem,calc(100%-2rem))] py-8 sm:w-[min(52rem,calc(100%-3rem))] sm:py-12' => auth()->user()->role === 'perusahaan', 'pembungkus halaman-aplikasi' => auth()->user()->role !== 'perusahaan'])>
        @unless (auth()->user()->role === 'perusahaan')
            <x-menu-peran :jenis-akun="auth()->user()->role" />
        @endunless
        <section @class(['min-w-0' => auth()->user()->role === 'perusahaan', 'isi-aplikasi' => auth()->user()->role !== 'perusahaan'])>
            @if (auth()->user()->role === 'perusahaan')
                <x-kepala-halaman label="Rekrutmen" judul="{{ $aksi }}" deskripsi="Lengkapi detail posisi agar kandidat memahami kesempatan yang ditawarkan.">
                    <a class="tombol-kedua" href="{{ route('lowongan.index') }}">Kembali ke lowongan</a>
                </x-kepala-halaman>
            @else
                <header class="kepala-halaman-aplikasi"><h1>{{ $aksi }}</h1></header>
            @endif
            <form method="POST" action="{{ $lowongan->exists ? route('lowongan.update', $lowongan) : route('lowongan.store') }}" @class(['grid gap-5 rounded-lg border border-slate-200 bg-white p-5 sm:grid-cols-2 sm:p-7' => auth()->user()->role === 'perusahaan', 'formulir-autentikasi' => auth()->user()->role !== 'perusahaan'])>
                @csrf
                @if ($lowongan->exists)
                    @method('PUT')
                @endif
                @include('lowongan.bagian-form')
                @if ($errors->any())
                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-900 sm:col-span-2" role="alert">{{ $errors->first() }}</div>
                @endif
                <button @class(['inline-flex min-h-11 w-fit items-center justify-center rounded-md bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700', 'sm:col-span-2' => auth()->user()->role === 'perusahaan', 'tombol' => auth()->user()->role !== 'perusahaan'])>{{ $aksi }}</button>
            </form>
        </section>
    </div>
</x-layout>