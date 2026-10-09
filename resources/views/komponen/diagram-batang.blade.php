{{--
    Diagram batang horizontal sederhana (CSS murni, tanpa library) - gaya yang sama
    dengan "Tren Belanja" di Dashboard Admin Logistik.

    Parameter:
      $judul      judul kartu
      $catatan    (opsional) keterangan kecil di bawah judul
      $label      array label tiap batang
      $nilai      array angka tiap batang (sejajar dengan $label)
      $rupiah     true = tampil "Rp ...", false = angka biasa (default true)
      $akhiran    (opsional) teks setelah angka, mis. ' unit'
      $warna      kelas warna batang: satu string, atau array per batang
--}}
@php
    $rupiah = $rupiah ?? true;
    $akhiran = $akhiran ?? '';
    $warna = $warna ?? 'bg-green';
    $nilai = array_values($nilai);
    $label = array_values($label);
    $maks = max(array_merge([0], $nilai)) ?: 1;
@endphp
<div class="bg-card border border-paperline rounded-lg p-4">
    <h3 class="font-display font-bold text-green">{{ $judul }}</h3>
    @isset($catatan)<p class="text-[11px] text-muted mt-0.5">{{ $catatan }}</p>@endisset
    <div class="space-y-1.5 mt-3">
        @forelse($label as $i => $teks)
        <div class="flex items-center gap-2 text-[11px]">
            <span class="w-24 shrink-0 text-muted truncate" title="{{ $teks }}">{{ $teks }}</span>
            <div class="flex-1 h-4 bg-paper rounded overflow-hidden">
                <div class="h-full rounded {{ is_array($warna) ? ($warna[$i] ?? 'bg-green') : $warna }}" style="width: {{ $nilai[$i] > 0 ? max(2, $nilai[$i] / $maks * 100) : 0 }}%"></div>
            </div>
            <span class="w-24 text-right font-mono">{{ $rupiah ? \App\Helpers\KalkulatorDapur::formatRupiah($nilai[$i]) : \App\Helpers\KalkulatorDapur::formatAngka($nilai[$i]).$akhiran }}</span>
        </div>
        @empty
        <div class="text-center text-xs text-muted py-4">Belum ada data.</div>
        @endforelse
    </div>
</div>
