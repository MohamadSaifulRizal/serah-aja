
<div x-data="{ buka: false, b: { id: null, nama: '', qty: 0, satuan: '' }, status: 'habis' }"
    @buka-lapor-bahan.window="b = $event.detail; status = 'habis'; buka = true">
    <div x-show="buka" class="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="buka = false">
            <h3 class="font-display font-bold text-base mb-1 text-green-deep">Lapor: <span x-text="b.nama"></span></h3>
            <p class="text-[11px] text-muted mb-3">Dikirim: <span x-text="b.qty + ' ' + (b.satuan || '')"></span></p>
            <form action="<?php echo e(route('dapur.simpanLaporan')); ?>" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="bahan_pokok_id" :value="b.id">
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Status Bahan</label>
                    <select name="status" x-model="status" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="habis">Habis Digunakan Semua</option>
                        <option value="sisa">Masih Ada Sisa</option>
                        <option value="kurang">Ternyata Kurang</option>
                    </select>
                </div>
                <div x-show="status === 'sisa'">
                    <label class="block text-xs text-muted uppercase mb-1">Jumlah Sisa</label>
                    <input type="number" step="0.01" min="0" name="jumlah_sisa" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div x-show="status === 'kurang'">
                    <label class="block text-xs text-muted uppercase mb-1">Jumlah Kekurangan</label>
                    <input type="number" step="0.01" min="0" name="jumlah_kurang" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Tanggal Lapor</label>
                    <input type="date" name="tanggal_lapor" required value="<?php echo e(date('Y-m-d')); ?>" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="buka = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-green text-white py-2 rounded-lg text-xs font-bold shadow-sm hover:bg-green-deep transition-colors">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\komponen\modal-lapor-bahan.blade.php ENDPATH**/ ?>