<?php

namespace App\Http\Controllers;

use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use App\Models\RiwayatPerbaikanInventaris;
use App\Support\LaporanInventaris;
use Illuminate\Http\Request;

class BarangInventarisController extends Controller
{
    public function indeks(Request $request)
    {
        // Admin Dapur: otomatis HANYA barang milik dapurnya sendiri (tanpa filter dapur).
        $dapurSaya = $this->dapurTerbatas($request);

        $query = BarangInventaris::with(['masterBarang.kategori', 'dapur', 'riwayatPerbaikan', 'pembaruKondisi', 'riwayatKondisi.pengguna']);

        if ($dapurSaya) {
            $query->where('dapur_id', $dapurSaya->id);
        } elseif ($request->filled('dapur_id')) {
            $query->where('dapur_id', $request->dapur_id);
        }

        $daftarBarang = $query->orderBy('tanggal_beli', 'desc')->get();

        $daftarDapur = $dapurSaya ? collect() : Dapur::orderBy('nama_dapur')->get();
        // Untuk dropdown "Nama Barang" di modal edit.
        $daftarMasterBarang = MasterBarang::where('jenis', 'inventaris')->orderBy('nama_barang')->get();

        $barangTerpilih = null;
        if ($request->filled('terpilih')) {
            $barangTerpilih = BarangInventaris::with(['riwayatPerbaikan', 'masterBarang.kategori', 'dapur', 'pembaruKondisi'])
                ->when($dapurSaya, fn ($q) => $q->where('dapur_id', $dapurSaya->id))
                ->find($request->terpilih);
        }

        return view('halaman.barang_inventaris', compact('daftarBarang', 'daftarDapur', 'daftarMasterBarang', 'barangTerpilih', 'dapurSaya'));
    }

    // Sama seperti Bahan Pokok: halaman tersendiri untuk input banyak barang
    // inventaris sekaligus dalam 1 transaksi pembelian. Nama Barang WAJIB
    // dipilih dari Master Barang (jenis "inventaris"), bisa langsung ditambah
    // lewat popup "+" tanpa pindah halaman kalau belum ada di katalog.
    public function formTambah(Request $request)
    {
        $dapurSaya = $this->dapurTerbatas($request);
        $daftarDapur = $dapurSaya ? collect([$dapurSaya]) : Dapur::orderBy('nama_dapur')->get();
        $daftarKategori = KategoriBarang::jenis('inventaris')->orderBy('nama_kategori')->get();
        $daftarMasterBarang = MasterBarang::where('jenis', 'inventaris')->orderBy('nama_barang')->get();

        return view('halaman.inventaris_tambah', compact('daftarDapur', 'daftarKategori', 'daftarMasterBarang', 'dapurSaya'));
    }

    public function simpanBatch(Request $request)
    {
        // Admin Dapur selalu mencatat untuk dapurnya sendiri, apa pun isi form-nya.
        if ($dapurSaya = $this->dapurTerbatas($request)) {
            $request->merge(['dapur_id' => $dapurSaya->id]);
        }

        $data = $request->validate([
            'dapur_id' => 'required|exists:dapur,id',
            'tanggal_beli' => 'required|date',
            'barang' => 'required|array|min:1',
            'barang.*.master_barang_id' => 'required|exists:master_barang,id',
            'barang.*.merk' => 'nullable|string|max:100',
            'barang.*.quantity' => 'required|numeric|min:0.01',
            'barang.*.harga_per_item' => 'required|numeric|min:0',
            'barang.*.status_barang' => 'required|in:barang_habis_pakai,barang_tetap',
            'barang.*.kondisi_barang' => 'required|in:baru,baik,rusak_ringan,rusak_berat',
            'barang.*.catatan' => 'nullable|string|max:255',
        ]);

        foreach ($data['barang'] as $baris) {
            $barang = BarangInventaris::create([
                'master_barang_id' => $baris['master_barang_id'],
                'merk' => $baris['merk'] ?? null,
                'dapur_id' => $data['dapur_id'],
                'tanggal_beli' => $data['tanggal_beli'],
                'quantity' => $baris['quantity'],
                'harga_per_item' => $baris['harga_per_item'],
                'status_barang' => $baris['status_barang'],
                'kondisi_barang' => $baris['kondisi_barang'],
                'catatan' => $baris['catatan'] ?? null,
            ]);

            // Kondisi saat barang pertama kali diinput juga dicatat (siapa & kapan).
            $barang->perbaruiKondisi($baris['kondisi_barang'], $request->user()->id, now()->toDateString(), 'Kondisi awal saat barang dicatat', awal: true);
        }

        $jumlah = count($data['barang']);

        return redirect()->route('inventaris.indeks')
            ->with('success', "{$jumlah} barang inventaris berhasil ditambahkan!");
    }

    // Export Excel data Barang Inventaris: SEMUA dapur (tanpa ?dapur_id) atau
    // dapur tertentu (?dapur_id=..). Dipanggil dari tombol "Export to Excel" di
    // halaman Barang Inventaris, jadi ikut pilihan filter dapur yang sedang aktif.
    public function exportExcel(Request $request)
    {
        $validasi = $request->validate(['dapur_id' => 'nullable|exists:dapur,id']);
        // Admin Dapur: selalu dapurnya sendiri, apa pun isi ?dapur_id.
        $dapur = $this->dapurTerbatas($request)
            ?? (! empty($validasi['dapur_id']) ? Dapur::find($validasi['dapur_id']) : null);

        return LaporanInventaris::unduh($dapur);
    }

    // Admin Dapur hanya boleh menyentuh barang milik dapurnya sendiri.
    private function pastikanMilikDapurSaya(Request $request, BarangInventaris $barangInventaris): void
    {
        $dapurSaya = $this->dapurTerbatas($request);

        abort_if($dapurSaya && $barangInventaris->dapur_id !== $dapurSaya->id, 403, 'Barang ini bukan milik dapur Anda.');
    }

    public function update(Request $request, BarangInventaris $barangInventaris)
    {
        $this->pastikanMilikDapurSaya($request, $barangInventaris);

        // Admin Dapur tidak bisa memindahkan barang ke dapur lain.
        if ($dapurSaya = $this->dapurTerbatas($request)) {
            $request->merge(['dapur_id' => $dapurSaya->id]);
        }

        $data = $request->validate([
            'master_barang_id' => 'required|exists:master_barang,id',
            'merk' => 'nullable|string|max:100',
            'dapur_id' => 'required|exists:dapur,id',
            'tanggal_beli' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'harga_per_item' => 'required|numeric|min:0',
            'status_barang' => 'required|in:barang_habis_pakai,barang_tetap',
            'kondisi_barang' => 'required|in:baru,baik,rusak_ringan,rusak_berat',
            'catatan' => 'nullable|string|max:255',
        ]);

        $kondisiBaru = $data['kondisi_barang'];
        unset($data['kondisi_barang']);

        $barangInventaris->update($data);

        // Kalau kondisi ikut berubah lewat form edit, tetap dicatat di riwayat (siapa & kapan).
        if ($kondisiBaru !== $barangInventaris->kondisi_barang) {
            $barangInventaris->perbaruiKondisi($kondisiBaru, $request->user()->id, now()->toDateString(), 'Diubah lewat Edit data barang');
        }

        return back()->with('success', 'Data berhasil diperbarui!');
    }

    // Update kondisi barang beserta tanggal & pencatat. Dipakai DUA sisi:
    //  - Admin Logistik / Super Admin: semua barang
    //  - Admin Dapur: HANYA barang milik dapurnya sendiri
    public function updateKondisi(Request $request, BarangInventaris $barangInventaris)
    {
        $user = $request->user();

        $this->pastikanMilikDapurSaya($request, $barangInventaris);

        $data = $request->validate([
            'kondisi_barang' => 'required|in:baru,baik,rusak_ringan,rusak_berat',
            'tanggal_update' => 'required|date|before_or_equal:today',
            'biaya' => 'required|numeric|min:0|max:999999999999',
            'catatan' => 'nullable|string|max:255',
        ], [
            'tanggal_update.before_or_equal' => 'Tanggal update tidak boleh di masa depan.',
            'biaya.required' => 'Biaya wajib diisi. Isi 0 jika tidak ada biaya.',
        ]);

        $barangInventaris->perbaruiKondisi($data['kondisi_barang'], $user->id, $data['tanggal_update'], $data['catatan'] ?? null, biaya: (float) $data['biaya']);

        return back()->with('success', "Kondisi \"{$barangInventaris->namaBarang()}\" diperbarui menjadi {$barangInventaris->labelKondisiBarang()}.");
    }

    public function hapus(Request $request, BarangInventaris $barangInventaris)
    {
        $this->pastikanMilikDapurSaya($request, $barangInventaris);

        $barangInventaris->delete();

        return back()->with('success', 'Barang inventaris berhasil dihapus!');
    }

    public function simpanRiwayat(Request $request)
    {
        $data = $request->validate([
            'barang_inventaris_id' => 'required|exists:barang_inventaris,id',
            'tanggal' => 'required|date',
            'jenis_aksi' => 'required|in:perawatan,perbaikan,penggantian',
            'biaya' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:150',
            'catatan' => 'nullable|string|max:255',
        ]);

        $this->pastikanMilikDapurSaya($request, BarangInventaris::findOrFail($data['barang_inventaris_id']));

        RiwayatPerbaikanInventaris::create($data);

        return redirect()->route('inventaris.indeks', ['terpilih' => $request->barang_inventaris_id])
            ->with('success', 'Riwayat berhasil dicatat!');
    }
}
