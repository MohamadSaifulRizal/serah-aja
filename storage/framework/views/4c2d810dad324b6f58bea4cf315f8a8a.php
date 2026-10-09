
<div x-data="{
        buka: false,
        d: { id: null, nama: '', dapur: '', kondisi: 'baik', riwayat: [] },
        kondisi: 'baik',
        aksi: <?php echo e(Js::from($aksi)); ?>,
        urutan: { rusak_berat: 0, rusak_ringan: 1, baik: 2, baru: 3 },
        get url() { return this.aksi.replace('__ID__', this.d.id); },
        rupiah(n) { return 'Rp' + Number(n || 0).toLocaleString('id-ID'); },
        // Label kolom biaya menyesuaikan perubahan yang dipilih (perbaikan / beli pengganti).
        get labelBiaya() {
            if (this.d.kondisi === 'rusak_berat' && this.kondisi === 'baru') return 'Biaya Membeli Barang Pengganti (Rp)';
            if (this.urutan[this.kondisi] > this.urutan[this.d.kondisi]) return 'Biaya Perbaikan (Rp)';
            return 'Biaya (Rp)';
        },
    }"
    @buka-update-kondisi.window="d = $event.detail; kondisi = d.kondisi; buka = true">
    <div x-show="buka" class="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="buka = false">
            <h3 class="font-display font-bold text-base text-green-deep">Update Kondisi Barang</h3>
            <p class="text-xs text-muted mb-3"><b x-text="d.nama"></b> &middot; <span x-text="d.dapur"></span></p>

            <form :action="url" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Kondisi Barang <span class="text-rust">*</span></label>
                    <select name="kondisi_barang" x-model="kondisi" required class="w-full border rounded p-2 text-xs bg-white">
                        <?php $__currentLoopData = \App\Models\BarangInventaris::DAFTAR_KONDISI; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nilai => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($nilai); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1"><span x-text="labelBiaya"></span> <span class="text-rust">*</span></label>
                    <input type="number" name="biaya" min="0" step="1" inputmode="numeric" required placeholder="mis. 150000" class="w-full border rounded p-2 text-xs bg-white">
                    <p class="text-[10px] text-muted mt-1">Wajib diisi, mis. <b>Rusak Ringan &rarr; Baik</b> = biaya perbaikan, <b>Rusak Berat &rarr; Baru</b> = biaya beli barang baru. Isi <b>0</b> jika tidak ada biaya.</p>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Tanggal Update <span class="text-rust">*</span></label>
                    <input type="date" name="tanggal_update" required value="<?php echo e(date('Y-m-d')); ?>" max="<?php echo e(date('Y-m-d')); ?>" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" maxlength="255" placeholder="mis. gagang pintu patah, sudah dilaporkan" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <p class="text-[11px] text-muted">Perubahan dicatat atas nama <b><?php echo e(auth()->user()->namaDanUsername()); ?></b> (<?php echo e(auth()->user()->labelRole()); ?>), sesuai akun yang sedang login.</p>
                <div class="flex gap-2">
                    <button type="button" @click="buka = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow-sm hover:bg-green-deep transition-colors">Simpan Kondisi</button>
                </div>
            </form>

            <h4 class="font-display font-bold text-green text-sm mt-5 mb-2 pt-3 border-t border-paperline">Riwayat Perubahan Kondisi</h4>
            <p class="text-[11px] text-muted mb-1" x-show="d.riwayat.length > 0">Total biaya update kondisi barang ini: <b class="font-mono text-rust" x-text="rupiah(d.riwayat.reduce((t, r) => t + Number(r.biaya || 0), 0))"></b></p>
            <div class="max-h-56 overflow-y-auto">
                <template x-for="(r, i) in d.riwayat" :key="i">
                    <div class="text-xs border-b border-paperline py-2">
                        <div class="flex justify-between gap-2">
                            <span class="font-semibold" x-text="r.dari ? (r.dari + ' → ' + r.ke) : ('Kondisi awal: ' + r.ke)"></span>
                            <span class="text-muted shrink-0" x-text="r.tanggal"></span>
                        </div>
                        <div class="text-muted">oleh <span x-text="r.oleh"></span><template x-if="r.catatan"><span x-text="' - ' + r.catatan"></span></template></div>
                        <div x-show="r.biaya > 0" class="font-mono font-semibold text-rust">Biaya: <span x-text="rupiah(r.biaya)"></span></div>
                    </div>
                </template>
                <p x-show="d.riwayat.length === 0" class="text-xs text-muted py-2">Belum ada riwayat perubahan kondisi.</p>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\komponen\modal-update-kondisi.blade.php ENDPATH**/ ?>