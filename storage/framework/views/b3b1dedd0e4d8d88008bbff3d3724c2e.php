<?php $__env->startSection('konten'); ?>
<div x-data="{
        bukaModal: false, bukaModalDetail: false,
        editId: null, editJenis: 'bahan_pokok', editNama: '', editKategori: '', editSatuan: '',
        detailJenis: '', detailNama: '', detailKategori: '', detailSatuan: '',
        // Kategori sekarang terpisah per jenis (Kategori Bahan Pokok / Kategori Barang
        // Inventaris), jadi pilihan kategori di form menyesuaikan Jenis yang dipilih.
        semuaKategori: <?php echo e(Illuminate\Support\Js::from($daftarKategori->map(fn ($k) => ['id' => $k->id, 'nama' => $k->nama_kategori, 'jenis' => $k->jenis])->values())); ?>,
        get kategoriTersedia() { return this.semuaKategori.filter(k => k.jenis === this.editJenis); },
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Master Barang</h1>
            <p class="text-xs text-muted mt-1">Katalog nama bahan supaya bisa langsung dipilih saat Tambah Transaksi. Kalau dihapus dari sini, riwayat transaksi lama yang sudah memakainya tetap aman (tidak ikut hilang).</p>
        </div>
        <button @click="bukaModal = true; editId = null; editJenis = 'bahan_pokok'; editNama = ''; editKategori = ''; editSatuan = ''"
            class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Barang
        </button>
    </header>

    <?php if(session('success')): ?>
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <?php echo $__env->make('komponen.hint-geser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto">
        <table class="w-full min-w-160 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Jenis</th>
                    <th class="p-3">Nama Barang</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Satuan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $daftarBarang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline">
                    <td class="p-3"><?php echo e($i + 1); ?></td>
                    <td class="p-3"><span class="text-[11px] px-2 py-0.5 rounded <?php echo e($b->jenis === 'inventaris' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep'); ?>"><?php echo e($b->labelJenis()); ?></span></td>
                    <td class="p-3 font-semibold"><?php echo e($b->nama_barang); ?></td>
                    <td class="p-3 text-xs"><?php echo e($b->kategori->nama_kategori ?? '-'); ?></td>
                    <td class="p-3 text-xs"><?php echo e($b->jenis === 'inventaris' ? '-' : ($b->satuan ?? '-')); ?></td>
                    <td class="p-3">
                        <button @click="bukaModalDetail = true;
                            editId = <?php echo e($b->id); ?>; editJenis = '<?php echo e($b->jenis); ?>'; editNama = '<?php echo e($b->nama_barang); ?>'; editKategori = '<?php echo e($b->kategori_id); ?>'; editSatuan = '<?php echo e($b->satuan); ?>';
                            detailJenis = '<?php echo e($b->labelJenis()); ?>'; detailNama = '<?php echo e($b->nama_barang); ?>'; detailKategori = '<?php echo e($b->kategori->nama_kategori ?? '-'); ?>'; detailSatuan = '<?php echo e($b->jenis === 'inventaris' ? '-' : ($b->satuan ?? '-')); ?>'"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="p-4 text-center text-xs text-muted">Belum ada master barang.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div x-show="bukaModalDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModalDetail = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Detail Barang</h3>
            <div class="space-y-2 text-xs mb-4">
                <div class="flex justify-between"><span class="text-muted">Jenis</span><b x-text="detailJenis"></b></div>
                <div class="flex justify-between"><span class="text-muted">Nama Barang</span><b x-text="detailNama"></b></div>
                <div class="flex justify-between"><span class="text-muted">Kategori</span><b x-text="detailKategori"></b></div>
                <div class="flex justify-between"><span class="text-muted">Satuan</span><b x-text="detailSatuan"></b></div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-paperline">
                <button type="button" @click="bukaModalDetail = false" class="flex-1 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Tutup</button>
                <button type="button" @click="bukaModalDetail = false; bukaModal = true" class="flex-1 bg-mustard text-ink py-2 rounded-lg text-xs font-bold">Edit</button>
                <form :action="`/master-barang/${editId}`" method="POST" onsubmit="return confirm('Hapus barang ini dari Master Barang?')" class="flex-1">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-rust text-white py-2 rounded-lg text-xs font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="editId ? 'Edit Barang' : 'Tambah Barang'"></h3>
            <form :action="editId ? `/master-barang/${editId}` : '<?php echo e(route('masterBarang.simpan')); ?>'" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>

                
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Jenis <span class="text-rust">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 border rounded p-2 text-xs cursor-pointer" :class="editJenis === 'bahan_pokok' ? 'border-green bg-green/10 font-bold text-green-deep' : 'border-paperline bg-white text-muted'">
                            <input type="radio" name="jenis" value="bahan_pokok" x-model="editJenis" @change="editKategori = ''" class="hidden"> Bahan Pokok
                        </label>
                        <label class="flex items-center justify-center gap-1.5 border rounded p-2 text-xs cursor-pointer" :class="editJenis === 'inventaris' ? 'border-mustard bg-mustard/10 font-bold text-mustard' : 'border-paperline bg-white text-muted'">
                            <input type="radio" name="jenis" value="inventaris" x-model="editJenis" @change="editKategori = ''" class="hidden"> Inventaris
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1" x-text="editJenis === 'inventaris' ? 'Nama Barang Inventaris' : 'Nama Bahan Pokok'"></label>
                    <input type="text" name="nama_barang" x-model="editNama" required placeholder="mis. Beras, Minyak Goreng" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Pilih Kategori</label>
                    <select name="kategori_id" x-model="editKategori" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        <template x-for="k in kategoriTersedia" :key="k.id">
                            <option :value="String(k.id)" x-text="k.nama" :selected="String(k.id) === String(editKategori)"></option>
                        </template>
                    </select>
                    <p class="text-[10px] text-muted mt-1" x-show="kategoriTersedia.length === 0" x-cloak>
                        Belum ada kategori untuk jenis ini - tambahkan dulu di menu
                        <b x-text="editJenis === 'inventaris' ? 'Kategori Barang Inventaris' : 'Kategori Bahan Pokok'"></b>.
                    </p>
                </div>
                
                <div x-show="editJenis === 'bahan_pokok'" x-cloak>
                    <label class="block text-xs text-muted uppercase mb-1">Satuan (opsional)</label>
                    <input type="text" name="satuan" x-model="editSatuan" placeholder="kg, liter, pcs, dst" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModal = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow hover:bg-green-deep transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\halaman\master_barang_indeks.blade.php ENDPATH**/ ?>