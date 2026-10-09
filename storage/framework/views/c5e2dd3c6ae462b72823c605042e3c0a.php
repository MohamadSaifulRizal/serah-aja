
<?php
    $rupiah = $rupiah ?? true;
    $akhiran = $akhiran ?? '';
    $warna = $warna ?? 'bg-green';
    $nilai = array_values($nilai);
    $label = array_values($label);
    $maks = max(array_merge([0], $nilai)) ?: 1;
?>
<div class="bg-card border border-paperline rounded-lg p-4">
    <h3 class="font-display font-bold text-green"><?php echo e($judul); ?></h3>
    <?php if(isset($catatan)): ?><p class="text-[11px] text-muted mt-0.5"><?php echo e($catatan); ?></p><?php endif; ?>
    <div class="space-y-1.5 mt-3">
        <?php $__empty_1 = true; $__currentLoopData = $label; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $teks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center gap-2 text-[11px]">
            <span class="w-24 shrink-0 text-muted truncate" title="<?php echo e($teks); ?>"><?php echo e($teks); ?></span>
            <div class="flex-1 h-4 bg-paper rounded overflow-hidden">
                <div class="h-full rounded <?php echo e(is_array($warna) ? ($warna[$i] ?? 'bg-green') : $warna); ?>" style="width: <?php echo e($nilai[$i] > 0 ? max(2, $nilai[$i] / $maks * 100) : 0); ?>%"></div>
            </div>
            <span class="w-24 text-right font-mono"><?php echo e($rupiah ? \App\Helpers\KalkulatorDapur::formatRupiah($nilai[$i]) : \App\Helpers\KalkulatorDapur::formatAngka($nilai[$i]).$akhiran); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-xs text-muted py-4">Belum ada data.</div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/komponen/diagram-batang.blade.php ENDPATH**/ ?>