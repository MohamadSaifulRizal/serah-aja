@extends('skema.aplikasi')
@section('konten')
<div x-data="{
        bukaModal: false, bukaModalDetail: false,
        editId: null, editNama: '',
        detailNama: '', detailJumlah: 0,
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <h1 class="font-display text-2xl font-bold text-green-deep">{{ $judul }}</h1>
        <button @click="bukaModal = true; editId = null; editNama = ''" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kategori
        </button>
    </header>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <p class="text-xs text-muted mb-3">
        @if($jenis === 'inventaris')
            Kategori ini khusus untuk Master Barang berjenis <b>Inventaris</b> (peralatan, perlengkapan, dll).
        @else
            Kategori ini khusus untuk Master Barang berjenis <b>Bahan Pokok</b> (sembako, protein, sayuran, dll).
        @endif
    </p>

    @include('komponen.hint-geser')
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto">
        <table class="w-full min-w-125 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Kategori</th>
                    <th class="p-3">Jumlah Master Barang</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarKategori as $i => $k)
                <tr class="border-b border-paperline">
                    <td class="p-3">{{ $i + 1 }}</td>
                    <td class="p-3 font-semibold">{{ $k->nama_kategori }}</td>
                    <td class="p-3 text-xs">
                        @if($k->master_barang_count > 0)
                            <span class="inline-block px-2 py-0.5 rounded {{ $jenis === 'inventaris' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep' }}">{{ $k->master_barang_count }} barang</span>
                        @else
                            <span class="text-muted">Belum dipakai</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <button @click="bukaModalDetail = true; editId = {{ $k->id }}; editNama = {{ Illuminate\Support\Js::from($k->nama_kategori) }}; detailNama = {{ Illuminate\Support\Js::from($k->nama_kategori) }}; detailJumlah = {{ $k->master_barang_count }}"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-4 text-center text-xs text-muted">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Detail --}}
    <div x-show="bukaModalDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModalDetail = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Detail Kategori</h3>
            <div class="space-y-2 text-xs mb-4">
                <div class="flex justify-between"><span class="text-muted">Nama Kategori</span><b x-text="detailNama"></b></div>
                <div class="flex justify-between"><span class="text-muted">Jenis</span><b>{{ $jenis === 'inventaris' ? 'Barang Inventaris' : 'Bahan Pokok' }}</b></div>
                <div class="flex justify-between"><span class="text-muted">Dipakai di Master Barang</span><b x-text="detailJumlah + ' barang'"></b></div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-paperline">
                <button type="button" @click="bukaModalDetail = false" class="flex-1 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Tutup</button>
                <button type="button" @click="bukaModalDetail = false; bukaModal = true" class="flex-1 bg-mustard text-ink py-2 rounded-lg text-xs font-bold">Edit</button>
                <form :action="`{{ route($namaRoute.'.indeks') }}/${editId}`" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-rust text-white py-2 rounded-lg text-xs font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
            <form :action="editId ? `{{ route($namaRoute.'.indeks') }}/${editId}` : '{{ route($namaRoute.'.simpan') }}'" method="POST" class="space-y-3">
                @csrf
                <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" x-model="editNama" required placeholder="{{ $contohNama }}" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModal = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
