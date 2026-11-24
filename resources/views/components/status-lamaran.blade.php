@props(['status'])

<span @class(['inline-flex min-h-7 items-center rounded px-2.5 text-xs font-semibold', 'bg-amber-50 text-amber-800' => $status === 'Dikirim', 'bg-blue-50 text-blue-800' => $status === 'Ditinjau', 'bg-red-50 text-red-800' => $status === 'Ditolak', 'bg-emerald-50 text-emerald-800' => $status === 'Diterima'])>{{ $status }}</span>