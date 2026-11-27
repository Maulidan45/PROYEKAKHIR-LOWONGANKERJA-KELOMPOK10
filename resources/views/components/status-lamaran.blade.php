@props(['status'])

<span @class([
    'inline-flex min-h-7 items-center rounded-full px-3 py-0.5 text-xs font-semibold whitespace-nowrap',
    'bg-slate-100 text-slate-700' => $status === 'Lamaran Terkirim',
    'bg-blue-50 text-blue-700' => $status === 'Seleksi Administrasi',
    'bg-indigo-50 text-indigo-700' => $status === 'Seleksi Berkas',
    'bg-amber-50 text-amber-800' => $status === 'Wawancara',
    'bg-emerald-50 text-emerald-800' => $status === 'Diterima',
    'bg-red-50 text-red-800' => $status === 'Ditolak',
])>{{ $status }}</span>
