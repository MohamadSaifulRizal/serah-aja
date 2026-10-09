@extends('skema.aplikasi')
@section('konten')
<div x-data="{
        dapurId: '',
        tanggalBeli: '{{ date('Y-m-d') }}',
        daftarMasterBarang: {{ Illuminate\Support\Js::from($daftarMasterBarang->map(fn($m) => [
            'id' => $m->id,
            'nama_barang' => $m->nama_barang,
        ])->values()) }},
        daftarBaris: [],
        barisKosong() {
            return { master_barang_id: '', merk: '', quantity: '', harga_per_item: '', status_barang: 'barang_habis_pakai', kondisi_barang: 'baru', catatan: '' };
        },
        tambahBaris() { this.daftarBaris.push(this.barisKosong()); },
        hapusBaris(idx) { if (this.daftarBaris.length > 1) this.daftarBaris.splice(idx, 1); },
        subtotal(baris) { return (parseFloat(baris.quantity) || 0) * (parseFloat(baris.harga_per_item) || 0); },
        totalItem() { return this.daftarBaris.length; },
        totalHarga() { return this.daftarBaris.reduce((total, b) => total + this.subtotal(b), 0); },
        // ==== Quick-add Nama Barang langsung dari halaman ini (icon +) ====
        // Sama pola dengan Tambah Transaksi Bahan Pokok, cuma di sini Master Barang
        // yang ditambah otomatis berjenis inventaris (tanpa input Satuan, karena
        // inventaris dihitung per unit bukan per kg/liter).
        quickAddBarisIdx: null,
        quickAddNama: '', quickAddKategori: '',
        quickAddError: '', quickAddLoading: false, quickAddOpen: false,
        bukaQuickAdd(idx) {
            this.quickAddOpen = true;
            this.quickAddBarisIdx = idx;
            this.quickAddError = '';
            this.quickAddNama = ''; this.quickAddKategori = '';
        },
        async simpanQuickAdd() {
            if (! this.quickAddNama) { this.quickAddError = 'Nama wajib diisi.'; return; }
            if (! this.quickAddKategori) { this.quickAddError = 'Kategori wajib dipilih.'; return; }
            this.quickAddLoading = true;
            this.quickAddError = '';
            try {
                const res = await fetch('{{ route('masterBarang.simpan') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ jenis: 'inventaris', nama_barang: this.quickAddNama, kategori_id: this.quickAddKategori }),
                });
                const json = await res.json();
                if (! res.ok) { this.quickAddError = Object.values(json.errors || {}).flat().join(' ') || 'Gagal menambah barang.'; this.quickAddLoading = false; return; }
                const baru = { id: json.masterBarang.id, nama_barang: json.masterBarang.nama_barang };
                this.daftarMasterBarang.push(baru);
                if (this.quickAddBarisIdx !== null) {
                    this.daftarBaris[this.quickAddBarisIdx].master_barang_id = String(baru.id);
                }
                this.quickAddOpen = false;
            } catch (e) {
                this.quickAddError = 'Terjadi kesalahan jaringan, coba lagi.';
            } finally {
                this.quickAddLoading = false;
            }
        },
    }" x-init="daftarBaris = [barisKosong()]">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('inventaris.indeks') }}" class="size-9 flex items-center justify-center rounded-lg border border-paperline bg-white hover:bg-paper transition-colors shrink-0">
            <svg class="size-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="font-display text-2xl font-bold text-green-deep">Tambah Transaksi Barang Inventaris</h1>
    </div>

    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('inventaris.simpanBatch') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Header transaksi: dapur pemilik + tanggal beli (berlaku untuk semua baris) --}}
        <div class="bg-card border border-paperline rounded-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-muted uppercase mb-1">Dapur Pemilik <span class="text-rust">*</span></label>
                @if($dapurSaya)
                    {{-- Admin Dapur: otomatis dapurnya sendiri --}}
                    <input type="hidden" name="dapur_id" value="{{ $dapurSaya->id }}">
                    <div class="w-full border rounded p-2 text-xs bg-paper font-semibold">{{ $dapurSaya->nama_dapur }}</div>
                @else
                <select name="dapur_id" x-model="dapurId" required class="w-full border rounded p-2 text-xs bg-white">
                    <option value="">-- Pilih Dapur --</option>
                    @foreach($daftarDapur as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_dapur }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div>
                <label class="block text-xs text-muted uppercase mb-1">Tanggal Beli</label>
                <input type="date" name="tanggal_beli" x-model="tanggalBeli" required class="w-full border rounded p-2 text-xs bg-white">
            </div>
        </div>

        {{-- Daftar barang: mirip Tambah Transaksi Bahan Pokok, tapi field menyesuaikan:
             tidak ada Supplier & Satuan (inventaris tidak punya keduanya), tapi ada
             Status Barang per baris (habis pakai / tetap). Kategori tidak ditampilkan
             sama sekali - otomatis ikut Master Barang lewat foreign key. --}}
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h4 class="font-display font-bold text-sm text-green mb-3">Daftar Barang</h4>

            @include('komponen.hint-geser')
            <div class="overflow-x-auto -mx-1">
                <table class="w-full min-w-260 text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="font-mono text-[10px] uppercase text-muted">
                            <th class="px-1 pb-1 w-56">Nama Barang</th>
                            <th class="px-1 pb-1 w-32">Merk</th>
                            <th class="px-1 pb-1 w-20">Qty</th>
                            <th class="px-1 pb-1 w-32">Harga/Item</th>
                            <th class="px-1 pb-1 w-44">Status Barang</th>
                            <th class="px-1 pb-1 w-36">Kondisi Barang</th>
                            <th class="px-1 pb-1 w-32">Subtotal</th>
                            <th class="px-1 pb-1 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(baris, idx) in daftarBaris" :key="idx">
                            <tr class="align-top">
                                <td class="px-1">
                                    <div class="flex items-center gap-1">
                                        <select :name="'barang[' + idx + '][master_barang_id]'" x-model="baris.master_barang_id" required class="w-full border rounded p-2 text-xs bg-white">
                                            <option value="">-- Pilih Nama Barang --</option>
                                            <template x-for="m in daftarMasterBarang" :key="m.id">
                                                <option :value="m.id" x-text="m.nama_barang"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="bukaQuickAdd(idx)" title="Tambah nama barang baru"
                                            class="shrink-0 size-7 flex items-center justify-center rounded-lg border border-dashed border-green text-green-deep hover:bg-green/10 transition-colors">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-1">
                                    <input type="text" :name="'barang[' + idx + '][merk]'" x-model="baris.merk" placeholder="Opsional" class="w-full border rounded p-2 text-xs bg-white">
                                </td>
                                <td class="px-1">
                                    <input type="number" step="0.01" min="0.01" :name="'barang[' + idx + '][quantity]'" x-model="baris.quantity" required class="w-full border rounded p-2 text-xs bg-white">
                                </td>
                                <td class="px-1">
                                    <input type="number" step="0.01" min="0" :name="'barang[' + idx + '][harga_per_item]'" x-model="baris.harga_per_item" required class="w-full border rounded p-2 text-xs bg-white">
                                </td>
                                <td class="px-1">
                                    <select :name="'barang[' + idx + '][status_barang]'" x-model="baris.status_barang" class="w-full border rounded p-2 text-xs bg-white">
                                        <option value="barang_habis_pakai">Habis Pakai</option>
                                        <option value="barang_tetap">Barang Tetap</option>
                                    </select>
                                </td>
                                <td class="px-1">
                                    <select :name="'barang[' + idx + '][kondisi_barang]'" x-model="baris.kondisi_barang" required class="w-full border rounded p-2 text-xs bg-white">
                                        @foreach(\App\Models\BarangInventaris::DAFTAR_KONDISI as $nilai => $label)
                                            <option value="{{ $nilai }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-1 pt-2 text-xs font-bold text-green-deep font-mono" x-text="'Rp' + subtotal(baris).toLocaleString('id-ID')"></td>
                                <td class="px-1 pt-1.5 text-center">
                                    <button type="button" x-show="daftarBaris.length > 1" @click="hapusBaris(idx)" class="text-rust hover:text-rust/70" title="Hapus baris ini">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="button" @click="tambahBaris()" class="mt-3 w-full flex items-center justify-center gap-1.5 border-2 border-dashed border-paperline text-green-deep text-xs font-bold py-2.5 rounded-lg hover:bg-green/5 transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Barang
            </button>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-card border border-paperline rounded-lg p-4 flex items-center justify-between flex-wrap gap-3">
            <div class="flex gap-6">
                <div>
                    <p class="text-[10px] text-muted uppercase">Total Barang</p>
                    <p class="text-sm font-bold text-ink" x-text="totalItem() + ' Barang'"></p>
                </div>
                <div>
                    <p class="text-[10px] text-muted uppercase">Total Keseluruhan</p>
                    <p class="text-sm font-bold text-green-deep" x-text="'Rp' + totalHarga().toLocaleString('id-ID')"></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('inventaris.indeks') }}" class="inline-flex items-center justify-center bg-white border border-paperline text-ink px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center bg-green text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan Semua Barang</button>
            </div>
        </div>
    </form>

    {{-- Modal Quick-Add Nama Barang (Master Barang jenis inventaris) --}}
    <div x-show="quickAddOpen" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="quickAddOpen = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Tambah Nama Barang Baru</h3>

            <p x-show="quickAddError" x-text="quickAddError" class="bg-rust/10 border border-rust text-rust text-xs px-3 py-2 rounded mb-3" x-cloak></p>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Barang <span class="text-rust">*</span></label>
                    <input type="text" x-model="quickAddNama" placeholder="mis. Kulkas 2 Pintu" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Kategori <span class="text-rust">*</span></label>
                    <select x-model="quickAddKategori" class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($daftarKategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="button" @click="quickAddOpen = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                <button type="button" @click="simpanQuickAdd()" :disabled="quickAddLoading" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors disabled:opacity-60" x-text="quickAddLoading ? 'Menyimpan...' : 'Simpan'"></button>
            </div>
        </div>
    </div>
</div>
@endsection
