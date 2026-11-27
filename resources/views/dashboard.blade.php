<x-layout :judul="'Dashboard | Lokerku'" :panel-admin="in_array($jenisAkun, ['admin', 'perusahaan', 'user'], true)">
    @if ($jenisAkun === 'admin')
        @include('dashboard-admin')
    @elseif ($jenisAkun === 'perusahaan')
        @include('dashboard-perusahaan')
    @else
        @include('dashboard-user')
    @endif
</x-layout>
