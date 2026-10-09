@php
    // $jenis: 'menu' atau 'transaksi'
    // $status: diajukan/disetujui/ditolak (menu) atau dipesan/diterima/dibatalkan (transaksi)
    $isPositif = in_array($status, ['disetujui', 'diterima']);
    $isNegatif = in_array($status, ['ditolak', 'dibatalkan']);
    $isPending = in_array($status, ['diajukan', 'dipesan']);

    $warna = $isPositif ? 'bg-green/15 text-green-deep border-green/30'
        : ($isNegatif ? 'bg-rust/15 text-rust border-rust/30' : 'bg-mustard/15 text-mustard border-mustard/30');

    $label = match($status) {
        'diajukan' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dipesan' => 'Dipesan',
        'diterima' => 'Diterima',
        'dibatalkan' => 'Dibatalkan',
        default => ucfirst($status),
    };
@endphp
<span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-1 rounded border whitespace-nowrap {{ $warna }}">
    @if($isPositif)
        <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    @elseif($isNegatif)
        <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 9l-6 6M9 9l6 6m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    @else
        <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    @endif
    {{ $label }}
</span>
