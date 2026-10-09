@extends('skema.aplikasi')
@section('konten')
<div>
    <header class="mb-6">
        <h1 class="font-display text-2xl font-bold text-green-deep">Dashboard Logistik</h1>
        <p class="text-xs text-muted mt-1">Ringkasan belanja bulan ini &amp; hal-hal yang perlu ditindaklanjuti.</p>
    </header>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Jumlah Dapur</div>
            <div class="font-mono text-xl font-bold">{{ $jumlahDapur }}</div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Belanja Bahan Pokok (Bulan Ini)</div>
            <div class="font-mono text-xl font-bold">{{ \App\Helpers\KalkulatorDapur::formatRupiah($totalBelanjaBahanBulanIni) }}</div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Belanja Inventaris (Bulan Ini)</div>
            <div class="font-mono text-xl font-bold">{{ \App\Helpers\KalkulatorDapur::formatRupiah($totalBelanjaInventarisBulanIni) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h3 class="font-display font-bold text-green mb-3">Bahan Belum Dilaporkan Admin Dapur</h3>
            <p class="text-[11px] text-muted mb-3">Bahan ini sudah dikirim ke dapur tapi belum ada laporan habis/sisa/kurang.</p>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($bahanBelumDilaporkan as $b)
                <div class="flex justify-between items-center text-xs p-2 bg-paper rounded border">
                    <div>
                        <b>{{ $b->namaBahan() }}</b>
                        <div class="text-[10px] text-muted">{{ $b->dapur->nama_dapur ?? 'Belum ada dapur tujuan' }} &middot; {{ $b->tanggal_transaksi->format('d-m-Y') }}</div>
                    </div>
                    <span class="font-mono">{{ \App\Helpers\KalkulatorDapur::formatAngka($b->quantity) }} {{ $b->satuanBahan() }}</span>
                </div>
                @empty
                <div class="text-center text-xs text-muted py-6">Semua transaksi sudah dilaporkan admin dapur.</div>
                @endforelse
            </div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h3 class="font-display font-bold text-green mb-3">Tren Belanja Bahan Pokok (6 Bulan Terakhir)</h3>
            <div class="space-y-1.5">
                @php($maks = max($dataBelanja) ?: 1)
                @foreach($labelBulan as $i => $label)
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="w-14 shrink-0 text-muted">{{ $label }}</span>
                    <div class="flex-1 h-4 bg-paper rounded overflow-hidden">
                        <div class="h-full bg-green rounded" style="width: {{ $maks > 0 ? ($dataBelanja[$i] / $maks * 100) : 0 }}%"></div>
                    </div>
                    <span class="w-24 text-right font-mono">{{ \App\Helpers\KalkulatorDapur::formatRupiah($dataBelanja[$i]) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
