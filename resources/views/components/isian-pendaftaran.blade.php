@props(['nama', 'keterangan', 'jenis' => 'text', 'pilihan' => [], 'wajib' => true, 'batas' => 255, 'minimal' => null])
<div class="isian">
    <label for="{{ $nama }}">{{ $keterangan }}{{ $wajib ? '' : ' (opsional)' }}</label>
    @if ($jenis === 'select')
        <select id="{{ $nama }}" name="{{ $nama }}" data-wajib="{{ $wajib ? 'true' : 'false' }}" @if ($errors->has($nama)) aria-invalid="true" aria-describedby="{{ $nama }}-error" @endif {{ $attributes }}>
            <option value="">Pilih {{ mb_strtolower($keterangan) }}</option>
            @foreach ($pilihan as $opsi)
                <option value="{{ $opsi }}" @selected(old($nama) === $opsi)>{{ $opsi }}</option>
            @endforeach
        </select>
    @elseif ($jenis === 'textarea')
        <textarea id="{{ $nama }}" name="{{ $nama }}" rows="4" maxlength="{{ $batas }}" @if ($minimal) minlength="{{ $minimal }}" @endif data-wajib="{{ $wajib ? 'true' : 'false' }}" @if ($errors->has($nama)) aria-invalid="true" aria-describedby="{{ $nama }}-error" @endif {{ $attributes }}>{{ is_string(old($nama)) ? old($nama) : '' }}</textarea>
    @else
        <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $jenis }}" value="{{ is_string(old($nama)) ? old($nama) : '' }}" maxlength="{{ $batas }}" data-wajib="{{ $wajib ? 'true' : 'false' }}" @if ($errors->has($nama)) aria-invalid="true" aria-describedby="{{ $nama }}-error" @endif {{ $attributes }}>
    @endif
    @error($nama)<p id="{{ $nama }}-error" class="kesalahan-isian">{{ $message }}</p>@enderror
</div>