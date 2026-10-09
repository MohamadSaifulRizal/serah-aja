<?php $__env->startSection('konten'); ?>
<div>
    <a href="<?php echo e(route('dapur.indeks')); ?>" class="text-xs text-muted hover:underline flex items-center gap-1 w-fit mb-4">&larr; Kembali ke Overview Dapur</a>

    <header class="mb-6">
        <h1 class="font-display text-2xl font-bold text-green-deep"><?php echo e($dapur->nama_dapur); ?></h1>
        <p class="text-xs text-muted mt-1"><?php echo e($dapur->alamat); ?> &middot; PJ: <?php echo e($dapur->penanggung_jawab ?? '-'); ?></p>
    </header>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-mustard/10 border border-mustard/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Biaya Perawatan Inventaris</p>
            <p class="font-mono text-lg font-bold text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($dapur->totalBiayaPerawatanInventaris())); ?></p>
        </div>
        <div class="bg-rust/10 border border-rust/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Biaya Update Kondisi Inventaris</p>
            <p class="font-mono text-lg font-bold text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($riwayatKondisi->sum('biaya'))); ?></p>
            <p class="text-[10px] text-muted mt-1">Perbaikan / beli pengganti saat kondisi barang diubah</p>
        </div>
        <div class="bg-green/10 border border-green/40 rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Transaksi Bahan Pokok</p>
            <p class="font-mono text-lg font-bold text-green-deep"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($dapur->totalTransaksiBahanPokok())); ?></p>
        </div>
        <div class="bg-card border border-paperline rounded-lg p-4">
            <p class="text-[11px] text-muted uppercase mb-1">Total Transaksi Barang Inventaris</p>
            <p class="font-mono text-lg font-bold text-ink"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($dapur->totalTransaksiBarangInventaris())); ?></p>
        </div>
    </div>

    <h3 class="font-display font-bold text-green mb-3">Transaksi Bahan Pokok yang Dikirim ke Dapur Ini</h3>
    <p class="text-[11px] text-muted -mt-2 mb-3">Ditampilkan per transaksi (1 baris = 1 kali kirim/beli, bisa berisi banyak bahan). Buka <b>Detail</b> untuk lihat rincian tiap bahan &amp; status laporannya.</p>
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto mb-6">
        <table class="w-full min-w-175 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">Tanggal</th><th class="p-3">Jumlah Bahan</th><th class="p-3">Total Harga</th><th class="p-3">Status Laporan Dapur</th><th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kelompokBahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline">
                    <td class="p-3 text-sm font-semibold">
                        <?php echo e($k->tanggal->translatedFormat('d M Y')); ?>

                        <?php if($k->ada_mandiri): ?>
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-mustard/20 text-mustard align-middle">ada Mandiri oleh Dapur</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 font-mono text-xs"><?php echo e($k->total_bahan); ?> Bahan</td>
                    <td class="p-3 font-mono text-xs font-bold"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($k->total_keseluruhan)); ?></td>
                    <td class="p-3">
                        <span class="text-[11px] px-2 py-0.5 rounded <?php echo e($k->sudah_dilaporkan === 0 ? 'bg-paper text-muted border border-paperline' : ($k->sudah_dilaporkan === $k->total_bahan ? 'bg-green/20 text-green-deep' : 'bg-mustard/20 text-mustard')); ?>">
                            <?php echo e($k->status_label); ?>

                        </span>
                    </td>
                    <td class="p-3">
                        <a href="<?php echo e(route('bahanPokok.detailKelompok', ['tanggal' => $k->tanggal->format('Y-m-d'), 'dapur_id' => $dapur->id])); ?>"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="p-4 text-center text-xs text-muted">Belum ada transaksi bahan untuk dapur ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
        <h3 class="font-display font-bold text-green">Barang Inventaris Milik Dapur Ini</h3>
        <a href="<?php echo e(route('inventaris.exportExcel', ['dapur_id' => $dapur->id])); ?>" class="inline-flex items-center bg-white border border-paperline text-ink text-[11px] font-bold px-3 py-1.5 rounded-lg hover:bg-paper transition-colors">Export to Excel</a>
    </div>
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto">
        <table class="w-full min-w-175 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">Nama Barang</th><th class="p-3">Status</th><th class="p-3">Kondisi</th><th class="p-3">Update Kondisi Terakhir</th><th class="p-3">Tgl Beli</th><th class="p-3">Total Harga</th><th class="p-3">Biaya Perawatan</th><th class="p-3">Biaya Update Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $dapur->barangInventaris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline">
                    <td class="p-3 font-semibold"><?php echo e($b->namaBarang()); ?></td>
                    <td class="p-3"><span class="text-[11px] px-2 py-0.5 rounded <?php echo e($b->status_barang === 'barang_tetap' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep'); ?>"><?php echo e($b->labelStatusBarang()); ?></span></td>
                    <td class="p-3"><span class="text-[11px] px-2 py-0.5 rounded font-semibold <?php echo e($b->kelasBadgeKondisi()); ?>"><?php echo e($b->labelKondisiBarang()); ?></span></td>
                    <td class="p-3 text-[11px] <?php echo e($b->kondisi_diperbarui_pada ? '' : 'text-muted'); ?>"><?php echo e($b->keteranganUpdateKondisi()); ?></td>
                    <td class="p-3 text-xs"><?php echo e($b->tanggal_beli->format('d-m-Y')); ?></td>
                    <td class="p-3 font-mono text-xs"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($b->total_harga)); ?></td>
                    <td class="p-3 font-mono text-xs text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($b->riwayatPerbaikan->sum('biaya'))); ?></td>
                    <td class="p-3 font-mono text-xs text-rust"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($b->totalBiayaUpdateKondisi())); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="p-4 text-center text-xs text-muted">Belum ada inventaris untuk dapur ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 class="font-display font-bold text-green mt-6 mb-1">Riwayat Update Kondisi &amp; Biayanya</h3>
    <p class="text-[11px] text-muted mb-3">Setiap perubahan kondisi barang inventaris dapur ini, lengkap dengan biaya yang dikeluarkan dan siapa yang mengupdate.</p>
    <?php echo $__env->make('komponen.tabel-riwayat-kondisi', ['riwayat' => $riwayatKondisi], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/halaman/dapur_detail.blade.php ENDPATH**/ ?>