<?php $__env->startSection('konten'); ?>
<div x-data="{
        bukaModal: false, bukaModalDetail: false,
        editId: null, editNama: '', editKontak: '', editAlamat: '',
        detailNama: '', detailKontak: '', detailAlamat: '', detailJumlah: 0,
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <h1 class="font-display text-2xl font-bold text-green-deep">Master Supplier</h1>
        <button @click="bukaModal = true; editId = null; editNama = ''; editKontak = ''; editAlamat = ''" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
            <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Supplier
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
        <table class="w-full min-w-125 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Supplier</th>
                    <th class="p-3">Kontak</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $daftarSupplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline">
                    <td class="p-3"><?php echo e($i + 1); ?></td>
                    <td class="p-3 font-semibold"><?php echo e($s->nama_supplier); ?></td>
                    <td class="p-3 text-xs"><?php echo e($s->kontak_supplier ?? '-'); ?></td>
                    <td class="p-3">
                        <button @click="bukaModalDetail = true; editId = <?php echo e($s->id); ?>; editNama = '<?php echo e($s->nama_supplier); ?>'; editKontak = '<?php echo e($s->kontak_supplier); ?>'; editAlamat = '<?php echo e($s->alamat); ?>'; detailNama = '<?php echo e($s->nama_supplier); ?>'; detailKontak = '<?php echo e($s->kontak_supplier); ?>'; detailAlamat = '<?php echo e($s->alamat); ?>'; detailJumlah = <?php echo e($s->bahan_pokok_count); ?>"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="p-4 text-center text-xs text-muted">Belum ada supplier.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div x-show="bukaModalDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModalDetail = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Detail Supplier</h3>
            <div class="space-y-2 text-xs mb-4">
                <div class="flex justify-between"><span class="text-muted">Nama Supplier</span><b x-text="detailNama"></b></div>
                <div class="flex justify-between"><span class="text-muted">Kontak</span><b x-text="detailKontak || '-'"></b></div>
                <div class="flex justify-between"><span class="text-muted">Alamat</span><b x-text="detailAlamat || '-'"></b></div>
                <div class="flex justify-between"><span class="text-muted">Jumlah Transaksi</span><b x-text="detailJumlah + 'x'"></b></div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-paperline">
                <button type="button" @click="bukaModalDetail = false" class="flex-1 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Tutup</button>
                <button type="button" @click="bukaModalDetail = false; bukaModal = true" class="flex-1 bg-mustard text-ink py-2 rounded-lg text-xs font-bold">Edit</button>
                <form :action="`/supplier/${editId}`" method="POST" onsubmit="return confirm('Hapus supplier ini?')" class="flex-1">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-rust text-white py-2 rounded-lg text-xs font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep" x-text="editId ? 'Edit Supplier' : 'Tambah Supplier'"></h3>
            <form :action="editId ? `/supplier/${editId}` : '<?php echo e(route('supplier.simpan')); ?>'" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Supplier</label>
                    <input type="text" name="nama_supplier" x-model="editNama" required class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Kontak (No. HP/WA)</label>
                    <input type="text" name="kontak_supplier" x-model="editKontak" placeholder="08xx-xxxx-xxxx" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Alamat</label>
                    <input type="text" name="alamat" x-model="editAlamat" class="w-full border rounded p-2 text-xs bg-white">
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

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\halaman\supplier_indeks.blade.php ENDPATH**/ ?>