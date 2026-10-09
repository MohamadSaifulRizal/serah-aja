
<?php echo $__env->make('komponen.hint-geser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="bg-card border border-paperline rounded-lg overflow-x-auto">
    <table class="w-full min-w-200 text-sm text-left">
        <thead>
            <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                <th class="p-3">Tanggal</th><th class="p-3">Barang</th><th class="p-3">Perubahan Kondisi</th><th class="p-3">Biaya</th><th class="p-3">Diupdate Oleh</th><th class="p-3">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-b border-paperline">
                <td class="p-3 text-xs"><?php echo e($r->tanggal_update->format('d-m-Y')); ?></td>
                <td class="p-3 font-semibold"><?php echo e($r->barangInventaris->namaBarang()); ?></td>
                <td class="p-3 text-xs"><?php echo e(\App\Models\BarangInventaris::labelKondisi($r->kondisi_sebelum)); ?> &rarr; <b><?php echo e(\App\Models\BarangInventaris::labelKondisi($r->kondisi_baru)); ?></b></td>
                <td class="p-3 font-mono text-xs <?php echo e($r->biaya > 0 ? 'text-rust font-bold' : 'text-muted'); ?>"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($r->biaya)); ?></td>
                <td class="p-3 text-xs"><?php echo e($r->namaPengguna()); ?></td>
                <td class="p-3 text-xs text-muted"><?php echo e($r->catatan ?: '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="p-4 text-center text-xs text-muted">Belum ada update kondisi barang inventaris.</td></tr>
            <?php endif; ?>
        </tbody>
        <?php if($riwayat->isNotEmpty()): ?>
        <tfoot>
            <tr class="bg-paper/60 font-bold">
                <td colspan="3" class="p-3 text-right text-xs uppercase text-muted">Total Biaya Update Kondisi</td>
                <td class="p-3 font-mono text-sm text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($riwayat->sum('biaya'))); ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\komponen\tabel-riwayat-kondisi.blade.php ENDPATH**/ ?>