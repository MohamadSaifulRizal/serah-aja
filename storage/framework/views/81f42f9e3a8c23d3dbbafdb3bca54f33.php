<?php $__env->startSection('konten'); ?>
<div x-data="{
        bukaModal: false, bukaModalRiwayat: false,
        bukaModalDetail: <?php echo e($barangTerpilih ? 'true' : 'false'); ?>,
        editId: null, editMasterBarang: '', editMerk: '', editDapur: '', editTanggal: '', editQty: '', editHarga: '', editStatus: 'barang_habis_pakai', editKondisi: 'baik', editCatatan: '',
    }">
    <header class="flex justify-between items-center mb-6 gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl font-bold text-green-deep">Barang Inventaris</h1>
            <?php if($dapurSaya): ?>
                <p class="text-xs text-muted mt-1">Dapur: <b><?php echo e($dapurSaya->nama_dapur); ?></b> &middot; hanya barang inventaris milik dapur Anda.</p>
            <?php endif; ?>
        </div>
        <div class="flex gap-2 flex-wrap">
            
            <a href="<?php echo e(route('inventaris.exportExcel', array_filter(['dapur_id' => request('dapur_id')]))); ?>"
                class="inline-flex items-center bg-white border border-paperline text-ink text-xs font-bold px-4 py-2 rounded-lg hover:bg-paper transition-colors">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"></path></svg>
                Export to Excel
            </a>
            <a href="<?php echo e(route('inventaris.formTambah')); ?>" class="inline-flex items-center bg-green text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-green-deep transition-colors">
                <svg class="size-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Transaksi
            </a>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="bg-green/10 border border-green text-green-deep text-xs px-4 py-2 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-rust/10 border border-rust text-rust text-xs px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <?php if (! ($dapurSaya)): ?>
    <form method="GET" class="mb-4">
        <select name="dapur_id" onchange="this.form.submit()" class="text-xs border border-paperline rounded-lg px-3 py-2 bg-white">
            <option value="">-- Semua Dapur --</option>
            <?php $__currentLoopData = $daftarDapur; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($d->id); ?>" <?php echo e(request('dapur_id') == $d->id ? 'selected' : ''); ?>><?php echo e($d->nama_dapur); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <span class="text-[11px] text-muted ml-2">Export Excel mengikuti pilihan ini: <b><?php echo e(request('dapur_id') ? ($daftarDapur->firstWhere('id', (int) request('dapur_id'))->nama_dapur ?? 'dapur terpilih') : 'Semua Dapur'); ?></b>.</span>
    </form>
    <?php endif; ?>

    <?php echo $__env->make('komponen.hint-geser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="bg-card border border-paperline rounded-lg overflow-x-auto">
        <table class="w-full min-w-295 text-sm text-left">
            <thead>
                <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Barang</th>
                    <th class="p-3">Dapur Pemilik</th>
                    <th class="p-3">Merk</th>
                    <th class="p-3">Tanggal Beli</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Harga/Item</th>
                    <th class="p-3">Status Barang</th>
                    <th class="p-3">Kondisi Barang</th>
                    <th class="p-3">Update Kondisi Terakhir</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $daftarBarang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-paperline <?php echo e($barangTerpilih && $barangTerpilih->id === $b->id ? 'bg-mustard/10' : ''); ?>">
                    <td class="p-3"><?php echo e($i + 1); ?></td>
                    <td class="p-3 font-semibold"><?php echo e($b->namaBarang()); ?></td>
                    <td class="p-3 text-xs"><?php echo e($b->dapur->nama_dapur ?? '-'); ?></td>
                    <td class="p-3 text-xs text-muted"><?php echo e($b->merk ?? '-'); ?></td>
                    <td class="p-3 text-xs"><?php echo e($b->tanggal_beli->format('d-m-Y')); ?></td>
                    <td class="p-3 font-mono text-xs"><?php echo e(\App\Helpers\KalkulatorDapur::formatAngka($b->quantity)); ?></td>
                    <td class="p-3 font-mono text-xs"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($b->harga_per_item)); ?></td>
                    <td class="p-3"><span class="text-[11px] px-2 py-0.5 rounded <?php echo e($b->status_barang === 'barang_tetap' ? 'bg-mustard/20 text-mustard' : 'bg-green/20 text-green-deep'); ?>"><?php echo e($b->labelStatusBarang()); ?></span></td>
                    <td class="p-3"><span class="text-[11px] px-2 py-0.5 rounded font-semibold <?php echo e($b->kelasBadgeKondisi()); ?>"><?php echo e($b->labelKondisiBarang()); ?></span></td>
                    <td class="p-3 text-[11px] <?php echo e($b->kondisi_diperbarui_pada ? '' : 'text-muted'); ?>"><?php echo e($b->keteranganUpdateKondisi()); ?></td>
                    <td class="p-3 whitespace-nowrap">
                        <button type="button" @click="$dispatch('buka-update-kondisi', <?php echo e(Js::from($b->dataModalKondisi())); ?>)"
                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-mustard rounded-lg hover:brightness-95 transition-all">Update Kondisi</button>
                        <a href="?terpilih=<?php echo e($b->id); ?>" class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper transition-colors">Detail</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="11" class="p-4 text-center text-xs text-muted">Belum ada barang inventaris.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($barangTerpilih): ?>
    <div x-show="bukaModalDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-lg p-5" @click.outside="bukaModalDetail = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Detail Barang Inventaris</h3>
            <div class="grid grid-cols-2 gap-2 text-xs mb-4">
                <div><span class="text-muted block">Nama Barang</span><b><?php echo e($barangTerpilih->namaBarang()); ?></b></div>
                <div><span class="text-muted block">Merk</span><b><?php echo e($barangTerpilih->merk ?? '-'); ?></b></div>
                <div><span class="text-muted block">Kategori</span><b><?php echo e($barangTerpilih->namaKategori()); ?></b></div>
                <div><span class="text-muted block">Dapur Pemilik</span><b><?php echo e($barangTerpilih->dapur->nama_dapur ?? '-'); ?></b></div>
                <div><span class="text-muted block">Status</span><b><?php echo e($barangTerpilih->labelStatusBarang()); ?></b></div>
                <div><span class="text-muted block">Kondisi Barang</span><span class="text-[11px] px-2 py-0.5 rounded font-semibold <?php echo e($barangTerpilih->kelasBadgeKondisi()); ?>"><?php echo e($barangTerpilih->labelKondisiBarang()); ?></span></div>
                <div class="col-span-2"><span class="text-muted block">Kondisi Terakhir Diupdate</span><b><?php echo e($barangTerpilih->keteranganUpdateKondisi()); ?></b></div>
                <div><span class="text-muted block">Tanggal Beli</span><b><?php echo e($barangTerpilih->tanggal_beli->format('d-m-Y')); ?></b></div>
                <div><span class="text-muted block">Quantity</span><b><?php echo e(\App\Helpers\KalkulatorDapur::formatAngka($barangTerpilih->quantity)); ?></b></div>
                <div><span class="text-muted block">Harga per Item</span><b><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($barangTerpilih->harga_per_item)); ?></b></div>
                <div><span class="text-muted block">Total Harga</span><b><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($barangTerpilih->total_harga)); ?></b></div>
                <div class="col-span-2"><span class="text-muted block">Catatan</span><b><?php echo e($barangTerpilih->catatan ?? '-'); ?></b></div>
            </div>

            <div class="flex justify-between items-center mb-2 pt-2 border-t border-paperline">
                <h4 class="font-display font-bold text-green text-sm">Riwayat Perawatan / Perbaikan / Penggantian</h4>
                <button type="button" @click="bukaModalRiwayat = true" class="inline-flex items-center bg-mustard text-ink text-[11px] font-bold px-3 py-1.5 rounded-lg">+ Catat Riwayat</button>
            </div>
            <div class="overflow-x-auto mb-4">
                <table class="w-full min-w-105 text-xs text-left">
                    <thead>
                        <tr class="bg-paper uppercase border-b border-paperline text-muted">
                            <th class="p-2">Tanggal</th><th class="p-2">Jenis</th><th class="p-2">Biaya</th><th class="p-2">Vendor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $barangTerpilih->riwayatPerbaikan->sortByDesc('tanggal'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-paperline">
                            <td class="p-2"><?php echo e($r->tanggal->format('d-m-Y')); ?></td>
                            <td class="p-2"><?php echo e($r->labelJenisAksi()); ?></td>
                            <td class="p-2 font-mono"><?php echo e(\App\Helpers\KalkulatorDapur::formatRupiah($r->biaya)); ?></td>
                            <td class="p-2"><?php echo e($r->vendor ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="p-3 text-center text-muted">Belum ada riwayat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-2 pt-2 border-t border-paperline">
                <a href="<?php echo e(route('inventaris.indeks')); ?>" class="flex-1 text-center bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Tutup</a>
                <button type="button" @click="$dispatch('buka-update-kondisi', <?php echo e(Js::from($barangTerpilih->load('riwayatKondisi.pengguna')->dataModalKondisi())); ?>)"
                    class="flex-1 bg-green text-white py-2 rounded-lg text-xs font-bold hover:bg-green-deep transition-colors">Update Kondisi</button>
                <button type="button" @click="bukaModal = true; editId=<?php echo e($barangTerpilih->id); ?>; editMasterBarang='<?php echo e($barangTerpilih->master_barang_id); ?>'; editMerk='<?php echo e($barangTerpilih->merk); ?>'; editDapur='<?php echo e($barangTerpilih->dapur_id); ?>'; editTanggal='<?php echo e($barangTerpilih->tanggal_beli->format('Y-m-d')); ?>'; editQty='<?php echo e($barangTerpilih->quantity); ?>'; editHarga='<?php echo e($barangTerpilih->harga_per_item); ?>'; editStatus='<?php echo e($barangTerpilih->status_barang); ?>'; editKondisi='<?php echo e($barangTerpilih->kondisi_barang); ?>'; editCatatan='<?php echo e($barangTerpilih->catatan); ?>'; bukaModalDetail = false"
                    class="flex-1 bg-mustard text-ink py-2 rounded-lg text-xs font-bold">Edit</button>
                <form action="<?php echo e(route('inventaris.hapus', $barangTerpilih->id)); ?>" method="POST" onsubmit="return confirm('Hapus barang ini?')" class="flex-1">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-rust text-white py-2 rounded-lg text-xs font-bold hover:bg-rust/85 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="bukaModalRiwayat" class="fixed inset-0 bg-black/40 z-60 flex items-center justify-center p-4" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-sm p-5" @click.outside="bukaModalRiwayat = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Catat Riwayat: <?php echo e($barangTerpilih->namaBarang()); ?></h3>
            <form action="<?php echo e(route('inventaris.riwayat')); ?>" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="barang_inventaris_id" value="<?php echo e($barangTerpilih->id); ?>">
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Jenis Tindakan</label>
                    <select name="jenis_aksi" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="perawatan">Perawatan Rutin (Pembersihan/Cek Berkala)</option>
                        <option value="perbaikan">Perbaikan (Servis Kerusakan)</option>
                        <option value="penggantian">Penggantian Suku Cadang (Sparepart)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required value="<?php echo e(date('Y-m-d')); ?>" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Biaya (Rp) <span class="text-rust">*wajib diisi</span></label>
                    <input type="number" step="0.01" min="0" name="biaya" required placeholder="0" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Vendor (opsional)</label>
                    <input type="text" name="vendor" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="bukaModalRiwayat = false" class="w-1/3 bg-white border border-paperline text-ink py-2 rounded-lg text-xs font-bold hover:bg-paper transition-colors">Batal</button>
                    <button type="submit" class="w-2/3 bg-mustard text-ink py-2 rounded-lg text-xs font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    
    <?php echo $__env->make('komponen.modal-update-kondisi', ['aksi' => route('inventaris.updateKondisi', '__ID__')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div x-show="bukaModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 overflow-y-auto py-8" x-cloak>
        <div class="bg-card border border-paperline rounded-lg w-full max-w-md p-5" @click.outside="bukaModal = false">
            <h3 class="font-display font-bold text-base mb-3 text-green-deep">Edit Barang Inventaris</h3>
            <form :action="`/logistik/barang-inventaris/${editId}`" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Nama Barang <span class="text-rust">*</span></label>
                    <select name="master_barang_id" x-model="editMasterBarang" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Nama Barang --</option>
                        <?php $__currentLoopData = $daftarMasterBarang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->nama_barang); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-[10px] text-muted mt-1">Kategori otomatis ikut dari Master Barang yang dipilih.</p>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Merk (opsional)</label>
                    <input type="text" name="merk" x-model="editMerk" class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <?php if($dapurSaya): ?>
                    <input type="hidden" name="dapur_id" value="<?php echo e($dapurSaya->id); ?>">
                <?php else: ?>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Dapur Pemilik <span class="text-rust">*</span></label>
                    <select name="dapur_id" x-model="editDapur" required class="w-full border rounded p-2 text-xs bg-white">
                        <option value="">-- Pilih Dapur --</option>
                        <?php $__currentLoopData = $daftarDapur; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d->id); ?>"><?php echo e($d->nama_dapur); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Status Barang</label>
                    <select name="status_barang" x-model="editStatus" class="w-full border rounded p-2 text-xs bg-white">
                        <option value="barang_habis_pakai">Barang Habis Pakai (mis. sabun, gas)</option>
                        <option value="barang_tetap">Barang Tetap (mis. kompor, kulkas)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Kondisi Barang <span class="text-rust">*</span></label>
                    <select name="kondisi_barang" x-model="editKondisi" required class="w-full border rounded p-2 text-xs bg-white">
                        <?php $__currentLoopData = \App\Models\BarangInventaris::DAFTAR_KONDISI; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nilai => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($nilai); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-muted uppercase mb-1">Tanggal Beli</label>
                        <input type="date" name="tanggal_beli" x-model="editTanggal" required class="w-full border rounded p-2 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase mb-1">Quantity</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" x-model="editQty" required class="w-full border rounded p-2 text-xs bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Harga per Item (Rp)</label>
                    <input type="number" step="0.01" min="0" name="harga_per_item" x-model="editHarga" required class="w-full border rounded p-2 text-xs bg-white">
                </div>
                <p class="text-[11px] text-muted">Total harga dihitung otomatis (quantity &times; harga per item).</p>
                <div>
                    <label class="block text-xs text-muted uppercase mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" x-model="editCatatan" class="w-full border rounded p-2 text-xs bg-white">
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

<?php echo $__env->make('skema.aplikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views/halaman/barang_inventaris.blade.php ENDPATH**/ ?>