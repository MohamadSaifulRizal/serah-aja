@extends('skema.aplikasi')
@section('konten')
<div x-data="{
        tanggal: '{{ date('Y-m-d') }}',
        daftarMasterBarang: {{ Illuminate\Support\Js::from($daftarMasterBarang->map(fn($m) => [
            'id' => $m->id,
            'nama_barang' => $m->nama_barang,
            'satuan' => $m->satuan,
        ])->values()) }},
        daftarSupplierJs: {{ Illuminate\Support\Js::from($daftarSupplier->map(fn($s) => [
            'id' => $s->id,
            'nama_supplier' => $s->nama_supplier,
        ])->values()) }},
        daftarBaris: [],
        barisKosong() {
            return { master_barang_id: '', merk: '', satuan: '', supplier_id: '', quantity: '', harga_per_item: '' };
        },
        tambahBaris() { this.daftarBaris.push(this.barisKosong()); },
        hapusBaris(idx) { if (this.daftarBaris.length > 1) this.daftarBaris.splice(idx, 1); },
        // Nama Bahan sekarang FOREIGN KEY ke Master Barang (master_barang_id dikirim
        // langsung apa adanya) - kategori otomatis ikut lewat relasi, tidak perlu
        // dikirim manual dari form ini.
        pilihBarang(idx) {
            const id = this.daftarBaris[idx].master_barang_id;
            const cocok = this.daftarMasterBarang.find(m => String(m.id) === String(id));
            this.daftarBaris[idx].satuan = cocok ? (cocok.satuan ?? '') : '';
        },
        subtotal(baris) { return (parseFloat(baris.quantity) || 0) * (parseFloat(baris.harga_per_item) || 0); },
        totalHarga() { return this.daftarBaris.reduce((total, b) => total + this.subtotal(b), 0); },
        // ==== Quick-add Nama Bahan / Supplier langsung dari halaman ini (icon +) ====
        quickAdd: null, // null | 'bahan' | 'supplier'
        quickAddBarisIdx: null,
        quickAddNama: '', quickAddKategori: '', quickAddSatuan: '', quickAddKontak: '', quickAddAlamat: '',
        quickAddError: '', quickAddLoading: false,
        bukaQuickAdd(jenis, idx) {
            this.quickAdd = jenis;
            this.quickAddBarisIdx = idx;
            this.quickAddError = '';
            this.quickAddNama = ''; this.quickAddKategori = ''; this.quickAddSatuan = '';
            this.quickAddKontak = ''; this.quickAddAlamat = '';
        },
        async simpanQuickAdd() {
            if (! this.quickAddNama) { this.quickAddError = 'Nama wajib diisi.'; return; }
            this.quickAddLoading = true;
            this.quickAddError = '';
            try {
                if (this.quickAdd === 'bahan') {
                    if (! this.quickAddKategori) { this.quickAddError = 'Kategori wajib dipilih.'; this.quickAddLoading = false; return; }
                    const res = await fetch('{{ route('masterBarang.simpan') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ jenis: 'bahan_pokok', nama_barang: this.quickAddNama, kategori_id: this.quickAddKategori, satuan: this.quickAddSatuan }),
                    });
                    const json = await res.json();
                    if (! res.ok) { this.quickAddError = Object.values(json.errors || {}).flat().join(' ') || 'Gagal menambah bahan.'; this.quickAddLoading = false; return; }
                    const baru = { id: json.masterBarang.id, nama_barang: json.masterBarang.nama_barang, satuan: json.masterBarang.satuan };
                    this.daftarMasterBarang.push(baru);
                    if (this.quickAddBarisIdx !== null) {
                        this.daftarBaris[this.quickAddBarisIdx].master_barang_id = String(baru.id);
                        this.pilihBarang(this.quickAddBarisIdx);
                    }
                } else if (this.quickAdd === 'supplier') {
                    const res = await fetch('{{ route('supplier.simpan') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ nama_supplier: this.quickAddNama, kontak_supplier: this.quickAddKontak, alamat: this.quickAddAlamat }),
                    });
                    const json = await res.json();
                    if (! res.ok) { this.quickAddError = Object.values(json.errors || {}).flat().join(' ') || 'Gagal menambah supplier.'; this.quickAddLoading = false; return; }
                    const baru = { id: json.supplier.id, nama_supplier: json.supplier.nama_supplier };
                    this.daftarSupplierJs.push(baru);
                    if (this.quickAddBarisIdx !== null) {
                        this.daftarBaris[this.quickAddBarisIdx].supplier_id = String(baru.id);
                    }
                }
                this.quickAdd = null;
            } catch (e) {
                this.quickAddError = 'Terjadi kesalahan jaringan, coba lagi.';
            } finally {
                this.quickAddLoading = false;
            }
        },
    }" x-init="daftarBaris = [barisKosong()]">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('bahanPokok.indeks') }}" class="size-9 flex items-center justify-center rounded-lg border border-paperline bg-white hover:bg-paper transition-colors shrink-0">
            <svg class="size-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Catat Pembelian Mandiri</h1>
            <p class="text-xs text-muted">Untuk: <b>{{ $dapur->nama_dapur }}</b> &middot; dipakai untuk kebutuhan bahan mendadak, baik menambah kiriman yang kurang maupun kebutuhan lain</p>
        </div>
    </div>

    <div class="bg-mustard/10 border border-mustard/40 text-ink text-xs px-4 py-2 rounded mb-4">
        Transaksi ini akan tercatat sebagai <b>pembelian mandiri oleh dapur</b> dan tetap bisa dilihat oleh admin logistik di Laporan Dapur.
    </div>

    @if($errors->any())
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('dapur.simpanBeliMandiri') }}" method="POST" class="space-y-4">
        @csrf

        <div class="bg-card border border-paperline rounded-lg p-4">
            <label class="block text-xs text-muted uppercase mb-1">Tanggal</label>
            <input type="date" name="tanggal_transaksi" x-model="tanggal" required class="w-full sm:w-64 border rounded p-2 text-xs bg-white">
        </div>

        <div class="bg-card border border-paperline rounded-lg p-4">
            <h4 class="font-display font-bold text-sm text-green mb-3">Daftar Bahan</h4>
            @include('komponen.hint-geser')
            <div class="overflow-x-auto -mx-1">
                <table class="w-full min-w-220 text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="font-mono text-[10px] uppercase text-muted">
                            <th class="px-1 pb-1 w-56">Nama Bahan</th>
                            <th class="px-1 pb-1 w-36">Merk</th>
                            <th class="px-1 pb-1 w-48">Supplier</th>
                            <th class="px-1 pb-1 w-24">Qty</th>
                            <th class="px-1 pb-1 w-32">Harga/Item</th>
                            <th class="px-1 pb-1 w-32">Subtotal</th>
                            <th class="px-1 pb-1 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(baris, idx) in daftarBaris" :key="idx">
                            <tr class="align-top">
                                <td class="px-1">
                                    <div class="flex items-center gap-1">
                                        <select :name="'bahan[' + idx + '][master_barang_id]'" x-model="baris.master_barang_id" @change="pilihBarang(idx)" required class="w-full border rounded p-2 text-xs bg-white">
                                            <option value="">-- Pilih Nama Bahan --</option>
                                            <template x-for="m in daftarMasterBarang" :key="m.id">
                                                <option :value="m.id" x-text="m.nama_barang"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="bukaQuickAdd('bahan', idx)" title="Tambah nama bahan baru"
                                            class="shrink-0 size-7 flex items-center justify-center rounded-lg border border-dashed border-green text-green-deep hover:bg-green/10 transition-colors">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-1">
                                    <input type="text" :name="'bahan[' + idx + '][merk]'" x-model="baris.merk" placeholder="Opsional" class="w-full border rounded p-2 text-xs bg-white">
                                </td>
                                <td class="px-1">
                                    <div class="flex items-center gap-1">
                                        <select :name="'bahan[' + idx + '][supplier_id]'" x-model="baris.supplier_id" required class="w-full border rounded p-2 text-xs bg-white">
                                            <option value="">-- Supplier --</option>
                                            <template x-for="s in daftarSupplierJs" :key="s.id">
                                                <option :value="s.id" x-text="s.nama_supplier"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="bukaQuickAdd('supplier', idx)" title="Tambah supplier baru"
                                            class="shrink-0 size-7 flex items-center justify-center rounded-lg border border-dashed border-green text-green-deep hover:bg-green/10 transition-colors">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-1">
                                    <div class="flex items-center gap-1">
                                        <input type="number" step="0.01" min="0.01" :name="'bahan[' + idx + '][quantity]'" x-model="baris.quantity" required class="w-full border rounded p-2 text-xs bg-white">
                                        <span class="text-[10px] text-muted whitespace-nowrap" x-text="baris.satuan"></span>
                                    </div>
                                </td>
                                <td class="px-1">
                                    <input type="number" step="0.01" min="0" :name="'bahan[' + idx + '][harga_per_item]'" x-model="baris.harga_per_item" required class="w-full border rounded p-2 text-xs bg-white">
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
                Tambah Bahan
            </button>
        </div>

        <div class="bg-card border border-paperline rounded-lg p-4 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-[10px] text-muted uppercase">Total Keseluruhan</p>
                <p class="text-sm font-bold text-green-deep" x-text="'Rp' + totalHarga().toLocaleString('id-ID')"></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bahanPokok.indeks') }}" class="inline-flex items-center justify-center bg-white border border-paperline text-ink px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center bg-green text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan</button>
            </div>
        </div>
    </form>

    {{-- Modal Quick-Add: Nama Bahan (Master Barang) atau Supplier, tanpa pindah halaman --}}
    <div x-show="quickAdd !== null" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="quickAdd = null">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="quickAdd === 'bahan' ? 'Tambah Nama Bahan Baru' : 'Tambah Supplier Baru'"></h3>

            <p x-show="quickAddError" x-text="quickAddError" class="bg-rust/10 border border-rust text-rust text-xs px-3 py-2 rounded mb-3" x-cloak></p>

            <div class="space-y-3">
                <template x-if="quickAdd === 'bahan'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-muted uppercase mb-1">Nama Bahan <span class="text-rust">*</span></label>
                            <input type="text" x-model="quickAddNama" placeholder="mis. Tempe" class="w-full border rounded p-2 text-xs bg-white">
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
                        <div>
                            <label class="block text-xs text-muted uppercase mb-1">Satuan (opsional)</label>
                            <input type="text" x-model="quickAddSatuan" placeholder="kg, liter, pcs, dst" class="w-full border rounded p-2 text-xs bg-white">
                        </div>
                    </div>
                </template>
                <template x-if="quickAdd === 'supplier'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-muted uppercase mb-1">Nama Supplier <span class="text-rust">*</span></label>
                            <input type="text" x-model="quickAddNama" placeholder="mis. Toko Sumber Rejeki" class="w-full border rounded p-2 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs text-muted uppercase mb-1">Kontak (opsional)</label>
                            <input type="text" x-model="quickAddKontak" placeholder="No HP / WA" class="w-full border rounded p-2 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs text-muted uppercase mb-1">Alamat (opsional)</label>
                            <input type="text" x-model="quickAddAlamat" class="w-full border rounded p-2 text-xs bg-white">
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="button" @click="quickAdd = null" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                <button type="button" @click="simpanQuickAdd()" :disabled="quickAddLoading" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors disabled:opacity-60" x-text="quickAddLoading ? 'Menyimpan...' : 'Simpan'"></button>
            </div>
        </div>
    </div>
</div>
@endsection
