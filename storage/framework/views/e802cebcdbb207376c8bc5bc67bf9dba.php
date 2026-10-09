<?php $__env->startSection('konten'); ?>
<div x-data="{
        bukaModal: false, bukaModalDetail: false,
        editId: null, editNama: '',
        detailNama: '', detailJumlah: 0,
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <h1 class="font-display text-2xl font-bold text-green-deep"><?php echo e($judul); ?></h1>
        <button @click="bukaModal = true; editId = null; editNama = ''" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kategori
        </button>
    </header>

    <?php if(session('success')): ?>
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <p class="text-xs text-muted mb-3">
        <?php if($jenis === 'inventaris'): ?>
            Kategori ini khusus untuk Master Barang berjenis <b>Inventaris</b> (peralatan, perlengkapan, dll).
        <?php else: ?>
            Kategori ini khusus untuk Master Barang berjenis <b>Bahan Pokok</b> (sembako, protein, sayuran, dll).
        <?php endif; ?>
    </p>

    <?php echo $__env->make('komponen.hint-geser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                <?php $__empty_1 = true; $__currentLoopData = $daftarKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline">
                    <td class="p-3"><?php echo e($i + 1); ?></td>
                    <td class="p-3 font-semibold"><?php echo e($k->nama_kategori); ?></td>
                    <td class="p-3 text-xs">
                        <?php if($k->master_barang_count > 0): ?>
                            <span class="inline-block px-2 py-0.5 rounded <?php echo e($jenis === 'inventaris' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep'); ?>"><?php echo e($k->master_barang_count); ?> barang</span>
                        <?php else: ?>
                            <span class="text-muted">Belum dipakai</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <button @click="bukaModalDetail = true; editId = <?php echo e($k->id); ?>; editNama = <?php echo e(Illuminate\Support\Js::from($k->nama_kategori)); ?>; detailNama = <?php echo e(Illuminate\Support\Js::from($k->nama_kategori)); ?>; detailJumlah = <?php echo e($k->master_barang_count); ?>"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="p-4 text-center text-xs text-muted">Belum ada kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div x-show="bukaModalDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModalDetail = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Detail Kategori</h3>
            <div class="space-y-2 text-xs mb-4">
                <div class="flex justify-between"><span class="text-muted">Nama Kategori</span><b x-text="detailNama"></b></div>
                <div class="flex justify-between"><span class="text-muted">Jenis</span><b><?php echo e($jenis === 'inventaris' ? 'Barang Inventaris' : 'Bahan Pokok'); ?></b></div>
                <div class="flex justify-between"><span class="text-muted">Dipakai di Master Barang</span><b x-text="detailJumlah + ' barang'"></b></div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-paperline">
                <button type="button" @click="bukaModalDetail = false" class="flex-1 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Tutup</button>
                <button type="button" @click="bukaModalDetail = false; bukaModal = true" class="flex-1 bg-mustard text-ink py-2 rounded-lg text-xs font-bold">Edit</button>
                <form :action="`<?php echo e(route($namaRoute.'.indeks')); ?>/${editId}`" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="flex-1">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-rust text-white py-2 rounded-lg text-xs font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
            <form :action="editId ? `<?php echo e(route($namaRoute.'.indeks')); ?>/${editId}` : '<?php echo e(route($namaRoute.'.simpan')); ?>'" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" x-model="editNama" required placeholder="<?php echo e($contohNama); ?>" class="w-full border rounded p-2 text-xs bg-white">
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

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/halaman/kategori_indeks.blade.php ENDPATH**/ ?>