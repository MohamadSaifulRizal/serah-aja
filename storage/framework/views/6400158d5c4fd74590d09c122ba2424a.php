<?php $__env->startSection('konten'); ?>
<div>
    <header class="mb-6">
        <h1 class="font-display text-2xl font-bold text-green-deep">Dashboard <?php echo e($dapur->nama_dapur); ?></h1>
        <p class="text-xs text-muted mt-1"><?php echo e($dapur->alamat); ?> &middot; PJ: <?php echo e($dapur->penanggung_jawab ?? '-'); ?></p>
        <p class="text-[11px] text-muted mt-1">Ringkasan dalam diagram khusus dapur Anda. Kelola datanya di menu <b>Bahan Pokok</b> dan <b>Barang Inventaris</b>.</p>
    </header>

    <?php if(session('success')): ?>
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-2 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-mustard/10 border border-mustard/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Biaya Perawatan Inventaris</p>
            <p class="font-mono text-lg font-bold text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($ringkasan->biayaPerawatan)); ?></p>
        </div>
        <div class="bg-rust/10 border border-rust/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Biaya Update Kondisi Inventaris</p>
            <p class="font-mono text-lg font-bold text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($ringkasan->biayaUpdateKondisi)); ?></p>
        </div>
        <div class="bg-green/10 border border-green/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Transaksi Bahan Pokok</p>
            <p class="font-mono text-lg font-bold text-green-deep"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($ringkasan->totalBahan)); ?></p>
            <p class="text-[10px] text-muted mt-1"><?php echo e($ringkasan->jumlahTransaksiBahan); ?> transaksi</p>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Transaksi Barang Inventaris</p>
            <p class="font-mono text-lg font-bold text-ink"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($ringkasan->totalInventaris)); ?></p>
            <p class="text-[10px] text-muted mt-1"><?php echo e($ringkasan->jumlahBarang); ?> barang</p>
        </div>
    </div>

    
    <h2 class="font-display font-bold text-green-deep mb-3">Bahan Pokok</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Tren Belanja Bahan Pokok (6 Bulan Terakhir)',
            'label' => $labelBulan, 'nilai' => $belanjaBahan, 'warna' => 'bg-green',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Kiriman Logistik vs Pembelian Mandiri',
            'catatan' => 'Perbandingan nilai bahan pokok berdasarkan asalnya.',
            'label' => ['Kiriman Logistik', 'Pembelian Mandiri'], 'nilai' => [$nilaiKiriman, $nilaiMandiri],
            'warna' => ['bg-green', 'bg-mustard'],
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Status Laporan Bahan Kiriman',
            'catatan' => 'Jumlah bahan kiriman logistik menurut status laporan dapur.',
            'label' => array_keys($statusLaporan), 'nilai' => array_values($statusLaporan),
            'rupiah' => false, 'akhiran' => ' bahan',
            'warna' => ['bg-paperline', 'bg-green', 'bg-mustard', 'bg-rust'],
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <h3 class="font-display font-bold text-green">Bahan Belum Dilaporkan</h3>
            <p class="text-[11px] text-muted mt-0.5 mb-3">Bahan kiriman yang belum Anda laporkan habis/sisa/kurang.</p>
            <div class="space-y-2 max-h-56 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $belumDilaporkan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('bahanPokok.detailKelompok', ['tanggal' => $b->tanggal_transaksi->format('Y-m-d'), 'dapur_id' => $dapur->id])); ?>"
                    class="flex justify-between items-center text-xs p-2 bg-paper rounded border hover:border-green/50 transition-colors">
                    <div>
                        <b><?php echo e($b->namaBahan()); ?></b>
                        <div class="text-[10px] text-muted"><?php echo e($b->tanggal_transaksi->format('d-m-Y')); ?> &middot; <?php echo e($b->supplier->nama_supplier ?? '-'); ?></div>
                    </div>
                    <span class="font-mono"><?php echo e(\App\Helpers\KalkulatorDapur::formatAngka($b->quantity)); ?> <?php echo e($b->satuanBahan()); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-xs text-muted py-6">Semua bahan kiriman sudah dilaporkan. Mantap!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <h2 class="font-display font-bold text-green-deep mb-3">Barang Inventaris</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Kondisi Barang Inventaris (jumlah unit)',
            'label' => array_values(\App\Models\BarangInventaris::DAFTAR_KONDISI), 'nilai' => $sebaranKondisi->values()->all(),
            'rupiah' => false, 'akhiran' => ' unit',
            'warna' => ['bg-green', 'bg-green/60', 'bg-mustard', 'bg-rust'],
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Tren Belanja Barang Inventaris (6 Bulan Terakhir)',
            'label' => $labelBulan, 'nilai' => $belanjaInventaris, 'warna' => 'bg-ink',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Biaya Update Kondisi (6 Bulan Terakhir)',
            'catatan' => 'Biaya perbaikan / beli pengganti saat kondisi barang diubah.',
            'label' => $labelBulan, 'nilai' => $biayaKondisiPerBulan, 'warna' => 'bg-rust',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('komponen.diagram-batang', [
            'judul' => 'Biaya Update Kondisi Terbesar per Barang',
            'catatan' => '5 barang dengan total biaya update kondisi tertinggi.',
            'label' => $biayaPerBarang->pluck('nama')->all(), 'nilai' => $biayaPerBarang->pluck('biaya')->all(), 'warna' => 'bg-rust',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="flex items-center justify-between gap-3 flex-wrap mb-1">
        <h3 class="font-display font-bold text-green">Update Kondisi Terbaru &amp; Biayanya</h3>
        <a href="<?php echo e(route('inventaris.indeks')); ?>" class="text-[11px] font-bold text-green-deep hover:underline">Kelola Barang Inventaris &rarr;</a>
    </div>
    <p class="text-[11px] text-muted mb-3">10 update terakhir, lengkap dengan biaya dan siapa yang mengupdate.</p>
    <?php echo $__env->make('komponen.tabel-riwayat-kondisi', ['riwayat' => $riwayatKondisi], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/halaman/dapur_beranda.blade.php ENDPATH**/ ?>