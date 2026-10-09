@extends('skema.aplikasi')
@section('konten')
<div x-data="{ bukaModal: false, bukaEdit: false, editId: null, editNama: '', editAlamat: '', editPJ: '', editCatatan: '' }">
    <header class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Overview Dapur</h1>
            <p class="text-xs text-muted mt-1">Pantau bahan &amp; inventaris yang dikirim ke tiap dapur.</p>
        </div>
        <button @click="bukaModal = true" type="button" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Dapur
        </button>
    </header>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($daftarDapur as $d)
        <div class="bg-card border border-paperline rounded-lg hover:border-green/40 hover:shadow-md transition-all flex flex-col">
            <a href="{{ route('dapur.detail', $d->id) }}" class="p-4 block flex-1">
                <h3 class="font-display font-bold text-green-deep text-base mb-1">{{ $d->nama_dapur }}</h3>
                <p class="text-xs text-muted mb-1">{{ $d->alamat ?: 'Alamat belum diisi' }}</p>
                <p class="text-xs text-muted mb-3">PJ: {{ $d->penanggung_jawab ?: 'belum diisi' }}</p>

                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-paperline">
                    <div>
                        <div class="text-[10px] text-muted uppercase">Transaksi Bahan</div>
                        <div class="font-mono font-bold text-sm">{{ $d->jumlah_transaksi_bahan }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-muted uppercase">Barang Inventaris</div>
                        <div class="font-mono font-bold text-sm">{{ $d->barang_inventaris_count }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-[10px] text-muted uppercase">Biaya Update Kondisi Inventaris</div>
                        <div class="font-mono font-bold text-sm text-rust">{{ \App\Helpers\KalkulatorDapur::formatRupiah($d->biaya_update_kondisi) }}</div>
                    </div>
                </div>
            </a>
            <div class="flex gap-2 px-4 pb-4">
                <button type="button"
                    @click="bukaEdit = true; editId = {{ $d->id }}; editNama = {{ Illuminate\Support\Js::from($d->nama_dapur) }}; editAlamat = {{ Illuminate\Support\Js::from($d->alamat) }}; editPJ = {{ Illuminate\Support\Js::from($d->penanggung_jawab) }}; editCatatan = {{ Illuminate\Support\Js::from($d->catatan) }}"
                    class="flex-1 bg-mustard text-ink py-1.5 rounded-lg text-[11px] font-bold">Edit</button>
                <form action="{{ route('dapur.hapus', $d->id) }}" method="POST" class="flex-1"
                    onsubmit="return confirm('Hapus dapur {{ e($d->nama_dapur) }}? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-rust text-white py-1.5 rounded-lg text-[11px] font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-muted py-12 bg-card border border-paperline rounded-lg text-sm">Belum ada data dapur.</div>
        @endforelse
    </div>

    {{-- Modal Tambah Dapur --}}
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-1 text-green-deep">Tambah Dapur Baru</h3>
            <p class="text-[11px] text-muted mb-3">Akun login Admin Dapur dibuat oleh Super Admin di menu Manajemen Akun.</p>
            <form action="{{ route('dapur.simpan') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Dapur <span class="text-rust">*</span></label>
                    <input type="text" name="nama_dapur" required placeholder="mis. Dapur Sukamaju" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Alamat <span class="text-rust">*</span></label>
                    <input type="text" name="alamat" required maxlength="255" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Penanggung Jawab <span class="text-rust">*</span></label>
                    <input type="text" name="penanggung_jawab" required maxlength="150" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="w-full border rounded p-2 text-xs bg-white">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModal = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan Dapur</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Dapur --}}
    <div x-show="bukaEdit" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="bukaEdit = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Edit Dapur</h3>
            <form :action="`{{ route('dapur.indeks') }}/${editId}`" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Dapur <span class="text-rust">*</span></label>
                    <input type="text" name="nama_dapur" x-model="editNama" required class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Alamat <span class="text-rust">*</span></label>
                    <input type="text" name="alamat" x-model="editAlamat" required maxlength="255" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Penanggung Jawab <span class="text-rust">*</span></label>
                    <input type="text" name="penanggung_jawab" x-model="editPJ" required maxlength="150" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" x-model="editCatatan" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaEdit = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
