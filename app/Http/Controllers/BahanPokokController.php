<?php

namespace App\Http\Controllers;

use App\Models\BahanPokok;
use App\Models\Dapur;
use App\Models\MasterBarang;
use App\Models\Supplier;
use App\Support\XlsxWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BahanPokokController extends Controller
{
    // Menu Bahan Pokok: LANGSUNG menampilkan transaksi (setiap kali admin
    // logistik belanja ke supplier = 1 baris baru), bukan master+stok terakumulasi.
    //
    // Tampilan awal berupa CARD per transaksi (dikelompokkan berdasarkan
    // tanggal_transaksi + dapur_id), bukan tabel datar per baris bahan.
    // Setiap card menampilkan tanggal, dapur tujuan, total jenis bahan, dan
    // total keseluruhan harga. Detail isi bahan baru ditampilkan (tabel) saat
    // card tersebut dibuka/dipencet.
    public function indeks(Request $request)
    {
        // Admin Dapur: otomatis HANYA transaksi dapurnya sendiri (tanpa filter dapur).
        $dapurSaya = $this->dapurTerbatas($request);

        $query = BahanPokok::with(['masterBarang.kategori', 'supplier', 'dapur', 'laporan']);

        if ($dapurSaya) {
            $query->where('dapur_id', $dapurSaya->id);
        } elseif ($request->filled('dapur_id')) {
            $query->where('dapur_id', $request->dapur_id);
        }

        $semuaTransaksi = $query->orderBy('tanggal_transaksi', 'desc')->orderBy('created_at', 'desc')->get();

        $daftarKelompok = $semuaTransaksi
            ->groupBy(fn ($t) => $t->tanggal_transaksi->format('Y-m-d').'_'.$t->dapur_id)
            ->map(function ($items) {
                return (object) [
                    'tanggal' => $items->first()->tanggal_transaksi,
                    'dapur' => $items->first()->dapur,
                    'items' => $items->values(),
                    'total_bahan' => $items->count(),
                    'total_keseluruhan' => $items->sum('total_harga'),
                    // Dipakai sisi Admin Dapur: berapa bahan kiriman yang sudah dilaporkan.
                    'perlu_laporan' => $items->where('sumber', 'logistik')->count(),
                    'sudah_dilaporkan' => $items->where('sumber', 'logistik')->filter(fn ($t) => $t->laporan !== null)->count(),
                    'ada_mandiri' => $items->contains(fn ($t) => $t->sumber === 'dapur'),
                ];
            })
            ->sortByDesc(fn ($k) => $k->tanggal->format('Y-m-d'))
            ->values();

        $daftarDapur = $dapurSaya ? collect() : Dapur::orderBy('nama_dapur')->get();

        return view('halaman.bahan_pokok', compact('daftarKelompok', 'daftarDapur', 'dapurSaya'));
    }

    // "Tambah Transaksi" sekarang halaman tersendiri (bukan popup), supaya input
    // banyak baris bahan sekaligus lebih lega. Nama bahan WAJIB dipilih dari
    // Master Barang (foreign key) - otomatis mengisi kategori/satuan, dan kalau
    // belum ada di katalog bisa langsung ditambah lewat popup "+" tanpa pindah
    // halaman.
    public function formTambah()
    {
        $daftarSupplier = Supplier::orderBy('nama_supplier')->get();
        $daftarDapur = Dapur::orderBy('nama_dapur')->get();
        $daftarKategori = \App\Models\KategoriBarang::jenis('bahan_pokok')->orderBy('nama_kategori')->get();
        // Cuma Master Barang jenis "bahan_pokok" yang relevan di sini (jenis
        // "inventaris" dikelola lewat menu Barang Inventaris).
        $daftarMasterBarang = MasterBarang::where('jenis', 'bahan_pokok')->orderBy('nama_barang')->get();

        return view('halaman.bahan_pokok_tambah', compact('daftarSupplier', 'daftarDapur', 'daftarMasterBarang', 'daftarKategori'));
    }

    // Halaman BARU (bukan dropdown/accordion) untuk 1 kelompok transaksi
    // (tanggal + dapur tujuan yang sama) - isinya detail semua bahan +
    // total harga keseluruhan, didesain rapi supaya gampang di-print / dijadikan
    // nota, atau diunduh sebagai file Excel (.xlsx).
    public function detailKelompok(Request $request)
    {
        $validasi = $request->validate([
            'tanggal' => 'required|date',
            'dapur_id' => 'required|exists:dapur,id',
        ]);

        $dapurSaya = $this->dapurTerbatas($request);
        abort_if($dapurSaya && (int) $validasi['dapur_id'] !== $dapurSaya->id, 403, 'Transaksi ini bukan milik dapur Anda.');

        $items = BahanPokok::with(['masterBarang.kategori', 'supplier', 'dapur', 'laporan'])
            ->whereDate('tanggal_transaksi', $validasi['tanggal'])
            ->where('dapur_id', $validasi['dapur_id'])
            ->get()
            ->sortBy(fn ($t) => $t->namaBahan())
            ->values();

        abort_if($items->isEmpty(), 404, 'Transaksi tidak ditemukan.');

        $tanggal = $items->first()->tanggal_transaksi;
        $dapur = $items->first()->dapur;
        $totalKeseluruhan = $items->sum('total_harga');

        $daftarSupplier = Supplier::orderBy('nama_supplier')->get();
        $daftarDapur = Dapur::orderBy('nama_dapur')->get();
        // Untuk dropdown "Nama Bahan" di modal edit - masih perlu Master Barang yang
        // sudah dihapus (kalau baris itu sedang memakainya) supaya tidak hilang dari
        // pilihan saat modal edit baris tersebut dibuka.
        $daftarMasterBarang = MasterBarang::where('jenis', 'bahan_pokok')->orderBy('nama_barang')->get();

        return view('halaman.bahan_pokok_detail', compact('items', 'tanggal', 'dapur', 'totalKeseluruhan', 'daftarSupplier', 'daftarDapur', 'daftarMasterBarang', 'dapurSaya'));
    }

    // Unduh isi 1 kelompok transaksi (1 tanggal + 1 dapur) sebagai file EXCEL
    // (.xlsx) - dipakai sebagai "nota" pembelian ke supplier.
    public function exportKelompok(Request $request)
    {
        $validasi = $request->validate([
            'tanggal' => 'required|date',
            'dapur_id' => 'required|exists:dapur,id',
        ]);

        $dapurSaya = $this->dapurTerbatas($request);
        abort_if($dapurSaya && (int) $validasi['dapur_id'] !== $dapurSaya->id, 403, 'Transaksi ini bukan milik dapur Anda.');

        $items = BahanPokok::with(['masterBarang.kategori', 'supplier', 'dapur', 'laporan'])
            ->whereDate('tanggal_transaksi', $validasi['tanggal'])
            ->where('dapur_id', $validasi['dapur_id'])
            ->get()
            ->sortBy(fn ($t) => $t->namaBahan())
            ->values();

        abort_if($items->isEmpty(), 404, 'Transaksi tidak ditemukan.');

        $namaDapur = $items->first()->dapur->nama_dapur ?? 'dapur';

        $x = new XlsxWriter();
        $s = $x->sheet('Detail Transaksi');
        $s->judul('Nota Pembelian Bahan Pokok')
            ->meta('Tanggal: '.$items->first()->tanggal_transaksi->translatedFormat('d F Y').' | Dapur tujuan: '.$namaDapur)
            ->kosong()
            ->header(['No', 'Nama Bahan', 'Merk', 'Kategori', 'Supplier', 'Qty', 'Satuan', 'Harga per Item', 'Total Harga', 'Status Laporan Dapur']);

        $tipe = ['tengah', 'teks', 'teks', 'teks', 'teks', 'angka', 'teks', 'uang', 'uang', 'teks'];
        foreach ($items as $i => $t) {
            $s->baris([
                $i + 1,
                $t->namaBahan(),
                $t->merk ?: '-',
                $t->namaKategori(),
                $t->supplier->nama_supplier ?? '-',
                (float) $t->quantity,
                $t->satuanBahan() ?: '-',
                (float) $t->harga_per_item,
                (float) $t->total_harga,
                $t->laporan ? $t->laporan->labelStatus() : 'Belum Lapor',
            ], $tipe);
        }
        $s->barisTotal(10, 7, 'Total Keseluruhan', 8, (float) $items->sum('total_harga'));
        $s->lebarKolom([6, 30, 16, 20, 24, 10, 10, 16, 18, 22]);

        return $x->unduh('nota-bahan-pokok-'.$validasi['tanggal'].'-'.Str::slug($namaDapur).'.xlsx');
    }

    // Export SEKALIGUS semua transaksi Bahan Pokok dalam 1 file Excel (semua
    // tanggal & dapur). Kalau di halaman Bahan Pokok sedang memilih 1 dapur,
    // tombolnya meneruskan ?dapur_id sehingga hanya dapur itu yang diexport.
    public function exportSemua(Request $request)
    {
        $validasi = $request->validate(['dapur_id' => 'nullable|exists:dapur,id']);
        // Admin Dapur: selalu dapurnya sendiri, apa pun isi ?dapur_id.
        $dapur = $this->dapurTerbatas($request)
            ?? (! empty($validasi['dapur_id']) ? Dapur::find($validasi['dapur_id']) : null);

        $query = BahanPokok::with(['masterBarang.kategori', 'supplier', 'dapur', 'laporan']);
        if ($dapur) {
            $query->where('dapur_id', $dapur->id);
        }

        $semua = $query->get()
            ->sortBy([
                fn ($a, $b) => $b->tanggal_transaksi <=> $a->tanggal_transaksi,
                fn ($a, $b) => strcasecmp($a->dapur->nama_dapur ?? '', $b->dapur->nama_dapur ?? ''),
                fn ($a, $b) => strcasecmp($a->namaBahan(), $b->namaBahan()),
            ])
            ->values();

        $labelDapur = $dapur ? $dapur->nama_dapur : 'Semua Dapur';

        $x = new XlsxWriter();

        // Sheet 1: semua baris bahan dari semua transaksi
        $s = $x->sheet('Semua Transaksi');
        $s->judul('Semua Transaksi Bahan Pokok - '.$labelDapur)
            ->meta('Diekspor pada '.now()->translatedFormat('d F Y H:i').' | Jumlah baris bahan: '.$semua->count())
            ->kosong()
            ->header(['No', 'Tanggal', 'Dapur Tujuan', 'Nama Bahan', 'Merk', 'Kategori', 'Supplier', 'Qty', 'Satuan', 'Harga per Item', 'Total Harga', 'Asal Transaksi', 'Status Laporan Dapur']);

        $tipe = ['tengah', 'tanggal', 'teks', 'teks', 'teks', 'teks', 'teks', 'angka', 'teks', 'uang', 'uang', 'teks', 'teks'];
        foreach ($semua as $i => $t) {
            $s->baris([
                $i + 1,
                $t->tanggal_transaksi,
                $t->dapur->nama_dapur ?? '-',
                $t->namaBahan(),
                $t->merk ?: '-',
                $t->namaKategori(),
                $t->supplier->nama_supplier ?? '-',
                (float) $t->quantity,
                $t->satuanBahan() ?: '-',
                (float) $t->harga_per_item,
                (float) $t->total_harga,
                $t->labelSumber(),
                $t->laporan ? $t->laporan->labelStatus() : 'Belum Lapor',
            ], $tipe);
        }
        $s->barisTotal(13, 9, 'Total Keseluruhan', 10, (float) $semua->sum('total_harga'));
        $s->lebarKolom([6, 14, 24, 30, 16, 20, 24, 10, 10, 16, 18, 20, 22]);

        // Sheet 2: rekap per transaksi (1 tanggal + 1 dapur = 1 baris), sama seperti card di halaman Bahan Pokok
        $r = $x->sheet('Rekap per Transaksi');
        $r->judul('Rekap per Transaksi Bahan Pokok - '.$labelDapur)
            ->kosong()
            ->header(['No', 'Tanggal', 'Dapur Tujuan', 'Jumlah Bahan', 'Total Keseluruhan']);

        $kelompok = $semua->groupBy(fn ($t) => $t->tanggal_transaksi->format('Y-m-d').'_'.$t->dapur_id)->values();
        foreach ($kelompok as $i => $items) {
            $r->baris([
                $i + 1,
                $items->first()->tanggal_transaksi,
                $items->first()->dapur->nama_dapur ?? '-',
                $items->count(),
                (float) $items->sum('total_harga'),
            ], ['tengah', 'tanggal', 'teks', 'angka', 'uang']);
        }
        $r->barisTotal(5, 3, 'Total Keseluruhan', 4, (float) $semua->sum('total_harga'));
        $r->lebarKolom([6, 14, 28, 14, 20]);

        $bagianNama = $dapur ? Str::slug($dapur->nama_dapur) : 'semua-dapur';

        return $x->unduh('transaksi-bahan-pokok-'.$bagianNama.'-'.now()->format('Ymd').'.xlsx');
    }

    // Admin Dapur hanya boleh mengubah/menghapus bahan yang DIA SENDIRI catat lewat
    // Pembelian Mandiri (sumber = dapur) di dapurnya. Bahan kiriman Admin Logistik
    // tidak boleh diubah dapur (cukup dilaporkan statusnya).
    private function pastikanBolehUbah(Request $request, BahanPokok $bahanPokok): void
    {
        $dapurSaya = $this->dapurTerbatas($request);

        abort_if(
            $dapurSaya && ($bahanPokok->dapur_id !== $dapurSaya->id || $bahanPokok->sumber !== 'dapur'),
            403,
            'Anda hanya bisa mengubah/menghapus bahan hasil Pembelian Mandiri dapur Anda sendiri.'
        );
    }

    public function update(Request $request, BahanPokok $bahanPokok)
    {
        $this->pastikanBolehUbah($request, $bahanPokok);

        $data = $request->validate([
            'master_barang_id' => 'required|exists:master_barang,id',
            'merk' => 'nullable|string|max:100',
            'supplier_id' => 'required|exists:supplier,id',
            'dapur_id' => 'required|exists:dapur,id',
            'tanggal_transaksi' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'harga_per_item' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
        ]);

        // Admin Dapur tidak bisa memindahkan bahan ke dapur lain.
        if ($dapurSaya = $this->dapurTerbatas($request)) {
            $data['dapur_id'] = $dapurSaya->id;
        }

        $bahanPokok->update($data);

        return back()->with('success', 'Data bahan pokok berhasil diperbarui!');
    }

    // "Tambah Transaksi" (card): 1 tanggal + 1 dapur tujuan, tapi bisa sekaligus
    // input banyak bahan di dalamnya (mirip alur "Tambah Produksi" - header dulu,
    // baru tambah item satu-satu pakai tombol +).
    public function simpanBatch(Request $request)
    {
        $data = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'dapur_id' => 'required|exists:dapur,id',
            'bahan' => 'required|array|min:1',
            'bahan.*.master_barang_id' => 'required|exists:master_barang,id',
            'bahan.*.merk' => 'nullable|string|max:100',
            'bahan.*.supplier_id' => 'required|exists:supplier,id',
            'bahan.*.quantity' => 'required|numeric|min:0.01',
            'bahan.*.harga_per_item' => 'required|numeric|min:0',
            'bahan.*.catatan' => 'nullable|string|max:255',
        ]);

        foreach ($data['bahan'] as $baris) {
            BahanPokok::create([
                'master_barang_id' => $baris['master_barang_id'],
                'merk' => $baris['merk'] ?? null,
                'supplier_id' => $baris['supplier_id'],
                'dapur_id' => $data['dapur_id'],
                'sumber' => 'logistik',
                'tanggal_transaksi' => $data['tanggal_transaksi'],
                'quantity' => $baris['quantity'],
                'harga_per_item' => $baris['harga_per_item'],
                'catatan' => $baris['catatan'] ?? null,
            ]);
        }

        $jumlah = count($data['bahan']);

        return redirect()->route('bahanPokok.indeks')
            ->with('success', "{$jumlah} bahan berhasil ditambahkan untuk transaksi tanggal ".\Carbon\Carbon::parse($data['tanggal_transaksi'])->translatedFormat('d M Y').".");
    }

    public function hapus(Request $request, BahanPokok $bahanPokok)
    {
        $this->pastikanBolehUbah($request, $bahanPokok);

        $bahanPokok->delete();

        return back()->with('success', 'Bahan pokok berhasil dihapus!');
    }
}
