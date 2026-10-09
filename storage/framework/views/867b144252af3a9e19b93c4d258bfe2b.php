<?php $__env->startSection('konten'); ?>
<div>
    <header class="mb-6">
        <h1 class="font-display text-2xl font-bold text-green-deep">Dashboard Logistik</h1>
        <p class="text-xs text-muted mt-1">Ringkasan belanja bulan ini &amp; hal-hal yang perlu ditindaklanjuti.</p>
    </header>

    <?php if(session('success')): ?>
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Jumlah Dapur</div>
            <div class="font-mono text-xl font-bold"><?php echo e($jumlahDapur); ?></div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Belanja Bahan Pokok (Bulan Ini)</div>
            <div class="font-mono text-xl font-bold"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($totalBelanjaBahanBulanIni)); ?></div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <div class="text-xs text-muted mb-1">Belanja Inventaris (Bulan Ini)</div>
            <div class="font-mono text-xl font-bold"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($totalBelanjaInventarisBulanIni)); ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h3 class="font-display font-bold text-green mb-3">Bahan Belum Dilaporkan Admin Dapur</h3>
            <p class="text-[11px] text-muted mb-3">Bahan ini sudah dikirim ke dapur tapi belum ada laporan habis/sisa/kurang.</p>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $bahanBelumDilaporkan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex justify-between items-center text-xs p-2 bg-paper rounded border">
                    <div>
                        <b><?php echo e($b->namaBahan()); ?></b>
                        <div class="text-[10px] text-muted"><?php echo e($b->dapur->nama_dapur ?? 'Belum ada dapur tujuan'); ?> &middot; <?php echo e($b->tanggal_transaksi->format('d-m-Y')); ?></div>
                    </div>
                    <span class="font-mono"><?php echo e(\App\Helpers\KalkulatorDapur::formatAngka($b->quantity)); ?> <?php echo e($b->satuanBahan()); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-xs text-muted py-6">Semua transaksi sudah dilaporkan admin dapur.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h3 class="font-display font-bold text-green mb-3">Tren Belanja Bahan Pokok (6 Bulan Terakhir)</h3>
            <div class="space-y-1.5">
                <?php ($maks = max($dataBelanja) ?: 1); ?>
                <?php $__currentLoopData = $labelBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="w-14 shrink-0 text-muted"><?php echo e($label); ?></span>
                    <div class="flex-1 h-4 bg-paper rounded overflow-hidden">
                        <div class="h-full bg-green rounded" style="width: <?php echo e($maks > 0 ? ($dataBelanja[$i] / $maks * 100) : 0); ?>%"></div>
                    </div>
                    <span class="w-24 text-right font-mono"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($dataBelanja[$i])); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\halaman\dashboard.blade.php ENDPATH**/ ?>