@extends('skema.aplikasi')
@section('konten')
<div>
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Bahan Pokok</h1>
            @if($dapurSaya)
                <p class="text-xs text-muted mt-1">Dapur: <b>{{ $dapurSaya->nama_dapur }}</b> &middot; bahan kiriman admin logistik &amp; pembelian mandiri dapur Anda.</p>
            @endif
        </div>
        <div class="flex gap-2 flex-wrap">
            {{-- Export SEMUA transaksi sekaligus (ikut filter dapur kalau sedang dipilih) --}}
            <a href="{{ route('bahanPokok.exportSemua', array_filter(['dapur_id' => request('dapur_id')])) }}"
                class="inline-flex items-center bg-white border border-paperline text-ink text-xs font-bold px-4 py-2 rounded-lg hover:bg-paper transition-colors">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"></path></svg>
                Export Semua Transaksi (Excel)
            </a>
            {{-- Admin Logistik: Tambah Transaksi. Admin Dapur: konsepnya Pembelian Mandiri. --}}
            <a href="{{ $dapurSaya ? route('dapur.formBeliMandiri') : route('bahanPokok.formTambah') }}" class="inline-flex items-center {{ $dapurSaya ? 'bg-mustard text-ink' : 'bg-green text-white' }} text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:brightness-95 transition-all">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                {{ $dapurSaya ? 'Catat Pembelian Mandiri' : 'Tambah Transaksi' }}
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @unless($dapurSaya)
    <form method="GET" class="mb-4">
        <select name="dapur_id" onchange="this.form.submit()" class="text-xs border border-paperline rounded-lg px-3 py-2 bg-white">
            <option value="">-- Semua Dapur --</option>
            @foreach($daftarDapur as $d)
                <option value="{{ $d->id }}" {{ request('dapur_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_dapur }}</option>
            @endforeach
        </select>
        <span class="text-[11px] text-muted ml-2">Export Excel mengikuti pilihan dapur ini ({{ request('dapur_id') ? 'dapur terpilih saja' : 'semua dapur' }}).</span>
    </form>
    @endunless

    {{-- Card per transaksi (dikelompokkan per tanggal + dapur tujuan). "Lihat Detail"
         membuka HALAMAN BARU (bukan dropdown di dalam card) supaya detail bahan +
         total harga keseluruhan bisa jadi nota / diunduh ke Excel dengan rapi. --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($daftarKelompok as $kelompok)
        <div class="bg-card border border-paperline rounded-lg p-5 flex flex-col">
            <p class="text-sm font-semibold text-ink mb-1">{{ $kelompok->tanggal->translatedFormat('d M Y') }}</p>
            <h4 class="font-display font-bold text-green-deep text-sm mb-4 truncate">Dapur: {{ $kelompok->dapur->nama_dapur ?? '-' }}</h4>
            @if($dapurSaya && $kelompok->ada_mandiri)
                <span class="self-start -mt-2 mb-3 text-[10px] px-1.5 py-0.5 rounded bg-mustard/20 text-mustard">ada Pembelian Mandiri</span>
            @endif

            <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="text-muted">Total Bahan</span>
                <span class="font-bold text-ink">{{ $kelompok->total_bahan }} Barang</span>
            </div>
            <div class="flex items-center justify-between text-xs mb-4">
                <span class="text-muted">Total Keseluruhan</span>
                <span class="font-bold text-green-deep">{{ \App\Helpers\KalkulatorDapur::formatRupiah($kelompok->total_keseluruhan) }}</span>
            </div>

            @if($dapurSaya && $kelompok->perlu_laporan > 0)
            <div class="flex items-center justify-between text-xs mb-4">
                <span class="text-muted">Status Laporan</span>
                <span class="text-[11px] px-2 py-0.5 rounded {{ $kelompok->sudah_dilaporkan === $kelompok->perlu_laporan ? 'bg-green/20 text-green-deep' : 'bg-mustard/20 text-mustard' }}">{{ $kelompok->sudah_dilaporkan }}/{{ $kelompok->perlu_laporan }} Dilaporkan</span>
            </div>
            @endif

            <a href="{{ route('bahanPokok.detailKelompok', ['tanggal' => $kelompok->tanggal->format('Y-m-d'), 'dapur_id' => $kelompok->dapur->id ?? '']) }}"
                class="mt-auto w-full flex items-center justify-between text-xs font-bold text-green-deep hover:underline pt-3 border-t border-paperline">
                <span>Lihat Detail</span>
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        @empty
        <div class="col-span-full bg-card border border-paperline rounded-lg p-6 text-center text-xs text-muted">Belum ada transaksi bahan pokok.</div>
        @endforelse
    </div>
</div>
@endsection
