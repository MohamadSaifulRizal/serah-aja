<?php
    // $status: habis / belum_terpenuhi / terpenuhi / tersedia
    $warna = match($status) {
        'habis' => 'bg-rust/15 text-rust border-rust/30',
        'belum_terpenuhi' => 'bg-mustard/15 text-mustard border-mustard/30',
        'terpenuhi' => 'bg-green/15 text-green-deep border-green/30',
        default => 'bg-green/15 text-green-deep border-green/30',
    };
    $label = match($status) {
        'habis' => 'Habis',
        'belum_terpenuhi' => 'Belum Terpenuhi',
        'terpenuhi' => 'Terpenuhi',
        default => 'Tersedia',
    };
?>
<span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded border whitespace-nowrap <?php echo e($warna); ?>">
    <svg class="size-3 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"></circle></svg>
    <?php echo e($label); ?>

</span>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\komponen\badge-stok.blade.php ENDPATH**/ ?>