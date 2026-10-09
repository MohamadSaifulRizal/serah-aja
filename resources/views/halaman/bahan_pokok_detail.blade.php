@extends('skema.aplikasi')
@section('konten')
<div x-data="{
        bukaModal: false,
        editId: null, editMasterBarang: '', editMerk: '', editDapur: '{{ $dapur->id ?? '' }}', editTanggal: '', editQty: '', editHarga: '',
        supplierDipilih: '',
    }">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap print:hidden">
        <div class="flex items-center gap-3">
            <a href="{{ route('bahanPokok.indeks') }}" class="size-9 flex items-center justify-center rounded-lg border border-paperline bg-white hover:bg-paper transition-colors shrink-0">
                <svg class="size-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="font-display text-2xl font-bold text-green-deep">Detail Transaksi Bahan Pokok</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bahanPokok.exportKelompok', ['tanggal' => $tanggal->format('Y-m-d'), 'dapur_id' => $dapur->id ?? '']) }}"
                class="inline-flex items-center bg-white border border-paperline text-ink text-xs font-bold px-4 py-2 rounded-lg hover:bg-paper transition-colors">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"></path></svg>
                Export to Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18h12v4H6v-4zM6 14h12v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2z"></path></svg>
                Cetak / Nota
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4 print:hidden">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4 print:hidden">{{ $errors->first() }}</div>
    @endif

    {{-- Kepala nota --}}
    <div class="bg-card border border-paperline rounded-lg p-5 mb-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-[10px] text-muted uppercase">Tanggal Transaksi</p>
            <p class="text-base font-bold text-ink">{{ $tanggal->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <p class="text-[10px] text-muted uppercase">Dapur Tujuan</p>
            <p class="text-base font-bold text-green-deep">{{ $dapur->nama_dapur ?? '-' }}</p>
        </div>
        <div>
            <p class="text-[10px] text-muted uppercase">Total Jenis Bahan</p>
            <p class="text-base font-bold text-ink">{{ $items->count() }} Barang</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-muted uppercase">Total Harga Keseluruhan</p>
            <p class="text-xl font-bold text-green-deep">{{ \App\Helpers\KalkulatorDapur::formatRupiah($totalKeseluruhan) }}</p>
        </div>
    </div>

    {{-- Isi nota: daftar bahan --}}
    @include('komponen.hint-geser')
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto mb-4">
        <table class="w-full min-w-220 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Bahan</th>
                    <th class="p-3">Merk</th>
                    <th class="p-3">Nama Supplier</th>
                    <th class="p-3">Kontak Supplier</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Harga/Item</th>
                    <th class="p-3">Total Harga</th>
                    <th class="p-3">Status Laporan Dapur</th>
                    <th class="p-3 print:hidden">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $t)
                <tr class="border-b border-paperline">
                    <td class="p-3">{{ $i + 1 }}</td>
                    <td class="p-3 font-semibold">{{ $t->namaBahan() }}</td>
                    <td class="p-3 text-xs text-muted">{{ $t->merk ?? '-' }}</td>
                    <td class="p-3 text-xs">{{ $t->supplier->nama_supplier ?? '-' }}</td>
                    <td class="p-3 text-xs">{{ $t->supplier->kontak_supplier ?? '-' }}</td>
                    <td class="p-3 font-mono text-xs">{{ \App\Helpers\KalkulatorDapur::formatAngka($t->quantity) }} {{ $t->satuanBahan() }}</td>
                    <td class="p-3 font-mono text-xs">{{ \App\Helpers\KalkulatorDapur::formatRupiah($t->harga_per_item) }}</td>
                    <td class="p-3 font-mono text-xs font-bold">{{ \App\Helpers\KalkulatorDapur::formatRupiah($t->total_harga) }}</td>
                    <td class="p-3">
                        @if($t->laporan)
                            <span class="text-[11px] px-2 py-0.5 rounded {{ $t->laporan->status === 'kurang' ? 'bg-rust/20 text-rust' : ($t->laporan->status === 'sisa' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep') }}">
                                {{ $t->laporan->labelStatus() }}
                                @if($t->laporan->status === 'sisa') ({{ \App\Helpers\KalkulatorDapur::formatAngka($t->laporan->jumlah_sisa) }} {{ $t->satuanBahan() }}) @endif
                                @if($t->laporan->status === 'kurang') ({{ \App\Helpers\KalkulatorDapur::formatAngka($t->laporan->jumlah_kurang) }} {{ $t->satuanBahan() }}) @endif
                            </span>
                        @else
                            <span class="text-[11px] px-2 py-0.5 rounded bg-paper text-muted border border-paperline">Belum Lapor</span>
                        @endif
                    </td>
                    <td class="p-3 print:hidden">
                        <div class="flex gap-1.5">
                            {{-- Admin Dapur: Lapor untuk bahan kiriman logistik; Edit/Hapus hanya untuk Pembelian Mandiri sendiri --}}
                            @if($dapurSaya && $t->sumber === 'logistik' && ! $t->laporan)
                                <button type="button" @click="$dispatch('buka-lapor-bahan', {{ Js::from(['id' => $t->id, 'nama' => $t->namaBahan(), 'qty' => $t->quantity, 'satuan' => $t->satuanBahan()]) }})"
                                    class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-white bg-green rounded-lg hover:bg-green-deep transition-colors">Lapor</button>
                            @endif
                            @if($dapurSaya && $t->sumber === 'dapur')
                                <span class="self-center text-[10px] px-1.5 py-0.5 rounded bg-mustard/20 text-mustard">Mandiri</span>
                            @endif
                            @if(! $dapurSaya || $t->sumber === 'dapur')
                            <button @click="bukaModal = true;
                                editId = {{ $t->id }}; editMasterBarang = '{{ $t->master_barang_id }}'; editMerk = '{{ $t->merk }}'; supplierDipilih = '{{ $t->supplier_id }}'; editDapur = '{{ $t->dapur_id }}'; editTanggal = '{{ $t->tanggal_transaksi->format('Y-m-d') }}'; editQty = '{{ $t->quantity }}'; editHarga = '{{ $t->harga_per_item }}'"
                                class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Edit</button>
                            <form action="{{ route('bahanPokok.hapus', $t->id) }}" method="POST" onsubmit="return confirm('Hapus bahan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-white bg-rust rounded-lg hover:bg-rust/85 transition-colors">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-paper/60 font-bold">
                    <td colspan="8" class="p-3 text-right text-xs uppercase text-muted">Total Harga Keseluruhan</td>
                    <td class="p-3 font-mono text-sm text-green-deep">{{ \App\Helpers\KalkulatorDapur::formatRupiah($totalKeseluruhan) }}</td>
                    <td class="print:hidden"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="text-[11px] text-muted print:hidden">Gunakan tombol <b>Export to Excel</b> di atas untuk mengunduh data ini sebagai file Excel (.xlsx), atau <b>Cetak / Nota</b> untuk mencetak sebagai bukti pembelian.</p>

    @if($dapurSaya)
        {{-- Popup Lapor Status Bahan (Admin Dapur) --}}
        @include('komponen.modal-lapor-bahan')
    @endif

    {{-- Modal Edit --}}
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8 print:hidden" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Edit Bahan Pokok</h3>
            <form :action="`/logistik/bahan-pokok/${editId}`" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Bahan <span class="text-rust">*</span></label>
                    <select name="master_barang_id" x-model="editMasterBarang" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Nama Bahan --</option>
                        @foreach($daftarMasterBarang as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_barang }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-muted mt-1">Kategori & satuan otomatis ikut dari Master Barang yang dipilih, tidak perlu diisi manual.</p>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Merk (opsional)</label>
                    <input type="text" name="merk" x-model="editMerk" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Supplier <span class="text-rust">*</span></label>
                    <select name="supplier_id" x-model="supplierDipilih" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($daftarSupplier as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_supplier }}{{ $s->kontak_supplier ? ' - '.$s->kontak_supplier : '' }}</option>
                        @endforeach
                    </select>
                </div>
                @if($dapurSaya)
                    <input type="hidden" name="dapur_id" value="{{ $dapurSaya->id }}">
                @else
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Dapur Tujuan <span class="text-rust">*</span></label>
                    <select name="dapur_id" x-model="editDapur" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Dapur --</option>
                        @foreach($daftarDapur as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_dapur }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Tanggal</label>
                    <input type="date" name="tanggal_transaksi" x-model="editTanggal" required class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-muted uppercase mb-1">Quantity</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" x-model="editQty" required class="w-full border rounded p-2 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase mb-1">Harga per Item (Rp)</label>
                        <input type="number" step="0.01" min="0" name="harga_per_item" x-model="editHarga" required class="w-full border rounded p-2 text-xs bg-white">
                    </div>
                </div>
                <p class="text-[11px] text-muted">Total harga dihitung otomatis (quantity &times; harga per item).</p>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModal = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
