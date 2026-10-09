<?php

namespace App\Http\Controllers;

use App\Models\BahanPokok;
use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\KategoriBarang;
use App\Models\LaporanPemakaianBahan;
use App\Models\MasterBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    // ==== Untuk ADMIN LOGISTIK: overview semua dapur (card grid) ====
    public function indeks()
    {
        // "Transaksi Bahan" yang ditampilkan di card HARUS berupa JUMLAH TRANSAKSI
        // (1 transaksi = 1 kali "Tambah Transaksi", bisa berisi banyak bahan
        // sekaligus), BUKAN jumlah baris bahan_pokok - makanya dihitung manual per
        // dapur pakai jumlahTransaksiBahanPokok(), bukan withCount('bahanPokok').
        $daftarDapur = Dapur::withCount('barangInventaris')->orderBy('nama_dapur')->get();
        $daftarDapur->each(function (Dapur $d) {
            $d->jumlah_transaksi_bahan = $d->jumlahTransaksiBahanPokok();
            $d->biaya_update_kondisi = $d->totalBiayaUpdateKondisiInventaris();
        });

        return view('halaman.dapur_indeks', compact('daftarDapur'));
    }

    // Alamat & Penanggung Jawab WAJIB diisi (baik saat tambah maupun edit).
    private function validasiDapur(Request $request): array
    {
        return $request->validate([
            'nama_dapur' => 'required|string|max:150',
            'alamat' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:150',
            'catatan' => 'nullable|string|max:255',
        ], [
            'alamat.required' => 'Alamat dapur wajib diisi.',
            'penanggung_jawab.required' => 'Penanggung jawab dapur wajib diisi.',
        ]);
    }

    // Tambah Dapur BARU (data dapur saja). Akun login Admin Dapur dibuat oleh
    // Super Admin di menu Manajemen Akun, lalu dikaitkan ke dapur ini.
    public function simpan(Request $request)
    {
        $data = $this->validasiDapur($request);

        Dapur::create($data);

        return back()->with('success', "Dapur \"{$data['nama_dapur']}\" berhasil ditambahkan! Minta Super Admin membuatkan akun Admin Dapur-nya di Manajemen Akun.");
    }

    public function update(Request $request, Dapur $dapur)
    {
        $data = $this->validasiDapur($request);

        $dapur->update($data);

        return back()->with('success', "Dapur \"{$dapur->nama_dapur}\" berhasil diperbarui!");
    }

    // Hapus dapur HANYA kalau belum punya transaksi bahan pokok / barang inventaris
    // (foreign key-nya restrict, jadi menghapus dapur berisi data akan merusak
    // histori) dan tidak ada akun Admin Dapur yang masih terkait dengannya.
    public function hapus(Dapur $dapur)
    {
        $nama = $dapur->nama_dapur;

        if ($dapur->bahanPokok()->exists() || $dapur->barangInventaris()->exists()) {
            return back()->withErrors(['hapus' => "Dapur \"{$nama}\" tidak bisa dihapus karena sudah punya transaksi Bahan Pokok atau Barang Inventaris."]);
        }

        if ($dapur->users()->exists()) {
            return back()->withErrors(['hapus' => "Dapur \"{$nama}\" tidak bisa dihapus karena masih terkait dengan akun Admin Dapur. Hapus atau pindahkan akunnya dulu di Manajemen Akun."]);
        }

        $dapur->delete();

        return back()->with('success', "Dapur \"{$nama}\" berhasil dihapus!");
    }

    // Detail 1 dapur (dilihat admin logistik): bahan yang dikirim + status laporan dari
    // admin dapur, plus daftar inventaris & biaya perawatannya. Ini yang menjawab kebutuhan
    // "logistik tahu dapur mana pakai bahan apa" tanpa perlu input stok keluar manual lagi.
    // Kolom "sumber" membedakan transaksi normal (dari Admin Logistik) dengan transaksi
    // yang dicatat MANDIRI oleh Admin Dapur sendiri (saat bahan kiriman ternyata kurang).
    public function detail(Dapur $dapur)
    {
        $dapur->load([
            'bahanPokok.masterBarang.kategori', 'bahanPokok.supplier', 'bahanPokok.laporan',
            'barangInventaris.masterBarang.kategori', 'barangInventaris.riwayatPerbaikan', 'barangInventaris.pembaruKondisi',
            'barangInventaris.riwayatKondisi',
        ]);

        // Riwayat update kondisi (selain pencatatan kondisi awal) beserta biayanya, terbaru di atas.
        $riwayatKondisi = $dapur->riwayatKondisiInventaris()
            ->whereNotNull('riwayat_kondisi_inventaris.kondisi_sebelum')
            ->with(['barangInventaris.masterBarang', 'pengguna'])
            ->orderByDesc('riwayat_kondisi_inventaris.tanggal_update')
            ->orderByDesc('riwayat_kondisi_inventaris.id')
            ->get();

        // "Transaksi Bahan Pokok yang Dikirim ke Dapur Ini" ditampilkan PER
        // TRANSAKSI (dikelompokkan per tanggal), bukan lagi per baris bahan -
        // supaya sinkron dengan cara Bahan Pokok dikelompokkan di menu Logistik.
        // Tiap kelompok juga menampilkan status laporan dapur secara keseluruhan
        // (berapa bahan yang sudah dilaporkan dari total bahan di transaksi itu),
        // baru kalau dibuka Detail-nya (halaman yang sama dengan Detail Kelompok
        // di menu Bahan Pokok) kelihatan rincian tiap bahan + status lapornya.
        $kelompokBahan = $dapur->bahanPokok
            ->groupBy(fn ($t) => $t->tanggal_transaksi->format('Y-m-d'))
            ->map(function ($items) {
                $totalBahan = $items->count();
                $sudahDilaporkan = $items->filter(fn ($t) => $t->laporan !== null)->count();

                return (object) [
                    'tanggal' => $items->first()->tanggal_transaksi,
                    'total_bahan' => $totalBahan,
                    'total_keseluruhan' => $items->sum('total_harga'),
                    'sudah_dilaporkan' => $sudahDilaporkan,
                    'ada_mandiri' => $items->contains(fn ($t) => $t->sumber === 'dapur'),
                    'status_label' => $sudahDilaporkan === 0
                        ? 'Belum Ada yang Dilaporkan'
                        : ($sudahDilaporkan === $totalBahan ? 'Lengkap Dilaporkan' : "{$sudahDilaporkan}/{$totalBahan} Dilaporkan"),
                ];
            })
            ->sortByDesc(fn ($k) => $k->tanggal->format('Y-m-d'))
            ->values();

        return view('halaman.dapur_detail', compact('dapur', 'kelompokBahan', 'riwayatKondisi'));
    }

    // ==== Untuk ADMIN DAPUR: Dashboard dapurnya sendiri ====
    // Isinya sama dengan halaman detail dapur di Management Dapur (ringkasan biaya,
    // transaksi bahan, kondisi & biaya inventaris, status laporan), tetapi dalam
    // bentuk DIAGRAM dan hanya mencakup dapur milik akun ini.
    // (Fitur Bahan Pokok & Barang Inventaris ada di menu masing-masing.)
    public function beranda(Request $request)
    {
        $dapur = $this->dapurTerbatas($request);

        $dapur->load([
            'bahanPokok.masterBarang.kategori', 'bahanPokok.supplier', 'bahanPokok.laporan',
            'barangInventaris.masterBarang.kategori', 'barangInventaris.riwayatPerbaikan', 'barangInventaris.riwayatKondisi',
        ]);

        // 6 bulan terakhir (label + kunci Y-m) untuk diagram tren.
        $bulan = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $labelBulan = $bulan->map(fn ($m) => $m->translatedFormat('M Y'))->all();
        $perBulan = fn ($koleksi, string $kolomTanggal, string $kolomNilai) => $bulan->map(
            fn ($m) => (float) $koleksi->filter(fn ($x) => $x->{$kolomTanggal}->format('Y-m') === $m->format('Y-m'))->sum($kolomNilai)
        )->all();

        $belanjaBahan = $perBulan($dapur->bahanPokok, 'tanggal_transaksi', 'total_harga');
        $belanjaInventaris = $perBulan($dapur->barangInventaris, 'tanggal_beli', 'total_harga');

        // Riwayat update kondisi (tanpa pencatatan kondisi awal) + biayanya.
        $riwayatKondisi = $dapur->riwayatKondisiInventaris()
            ->whereNotNull('riwayat_kondisi_inventaris.kondisi_sebelum')
            ->with(['barangInventaris.masterBarang', 'pengguna'])
            ->orderByDesc('riwayat_kondisi_inventaris.tanggal_update')
            ->orderByDesc('riwayat_kondisi_inventaris.id')
            ->get();
        $biayaKondisiPerBulan = $perBulan($riwayatKondisi, 'tanggal_update', 'biaya');

        // Biaya update kondisi terbesar per barang (5 teratas).
        $biayaPerBarang = $riwayatKondisi->where('biaya', '>', 0)
            ->groupBy('barang_inventaris_id')
            ->map(fn ($rows) => ['nama' => $rows->first()->barangInventaris->namaBarang(), 'biaya' => (float) $rows->sum('biaya')])
            ->sortByDesc('biaya')->take(5)->values();

        // Sebaran kondisi barang inventaris (jumlah unit).
        $sebaranKondisi = collect(BarangInventaris::DAFTAR_KONDISI)->map(
            fn ($label, $kode) => (float) $dapur->barangInventaris->where('kondisi_barang', $kode)->sum('quantity')
        );

        // Bahan pokok: kiriman logistik vs pembelian mandiri, serta status laporan kiriman.
        $nilaiKiriman = (float) $dapur->bahanPokok->where('sumber', 'logistik')->sum('total_harga');
        $nilaiMandiri = (float) $dapur->bahanPokok->where('sumber', 'dapur')->sum('total_harga');

        $kiriman = $dapur->bahanPokok->where('sumber', 'logistik');
        $belumDilaporkan = $kiriman->filter(fn ($t) => $t->laporan === null)->sortBy('tanggal_transaksi')->values();
        $statusLaporan = [
            'Belum Dilaporkan' => $belumDilaporkan->count(),
            'Habis' => $kiriman->filter(fn ($t) => $t->laporan?->status === 'habis')->count(),
            'Masih Ada Sisa' => $kiriman->filter(fn ($t) => $t->laporan?->status === 'sisa')->count(),
            'Ternyata Kurang' => $kiriman->filter(fn ($t) => $t->laporan?->status === 'kurang')->count(),
        ];

        $ringkasan = (object) [
            'biayaPerawatan' => $dapur->totalBiayaPerawatanInventaris(),
            'biayaUpdateKondisi' => (float) $riwayatKondisi->sum('biaya'),
            'totalBahan' => $dapur->totalTransaksiBahanPokok(),
            'totalInventaris' => $dapur->totalTransaksiBarangInventaris(),
            'jumlahTransaksiBahan' => $dapur->jumlahTransaksiBahanPokok(),
            'jumlahBarang' => $dapur->barangInventaris->count(),
        ];

        return view('halaman.dapur_beranda', [
            'dapur' => $dapur,
            'ringkasan' => $ringkasan,
            'labelBulan' => $labelBulan,
            'belanjaBahan' => $belanjaBahan,
            'belanjaInventaris' => $belanjaInventaris,
            'biayaKondisiPerBulan' => $biayaKondisiPerBulan,
            'biayaPerBarang' => $biayaPerBarang,
            'sebaranKondisi' => $sebaranKondisi,
            'nilaiKiriman' => $nilaiKiriman,
            'nilaiMandiri' => $nilaiMandiri,
            'statusLaporan' => $statusLaporan,
            'belumDilaporkan' => $belumDilaporkan,
            'riwayatKondisi' => $riwayatKondisi->take(10),
        ]);
    }

    // Admin dapur lapor status 1 transaksi bahan pokok: habis / masih sisa / malah kurang.
    public function simpanLaporan(Request $request)
    {
        $data = $request->validate([
            'bahan_pokok_id' => 'required|exists:bahan_pokok,id',
            'status' => 'required|in:habis,sisa,kurang',
            'jumlah_sisa' => 'nullable|numeric|min:0',
            'jumlah_kurang' => 'nullable|numeric|min:0',
            'tanggal_lapor' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ]);

        $bahanPokok = BahanPokok::findOrFail($data['bahan_pokok_id']);

        // Pastikan admin dapur cuma bisa lapor bahan yang benar-benar dikirim ke dapurnya sendiri.
        abort_if($bahanPokok->dapur_id !== $request->user()->dapur_id, 403, 'Bahan ini bukan untuk dapur Anda.');

        LaporanPemakaianBahan::create($data + ['dilaporkan_oleh' => $request->user()->id]);

        return back()->with('success', 'Laporan pemakaian berhasil dikirim ke admin logistik!');
    }

    // Admin dapur bisa catat pembelian bahan sendiri kapan saja - BUKAN cuma kalau
    // bahan kiriman admin logistik ternyata kurang, tapi juga untuk kebutuhan
    // mendadak lain yang tidak bisa diprediksi sebelumnya (dapur yang paling tahu
    // kebutuhan hariannya). Mirip alur Tambah Transaksi di Bahan Pokok, tapi dapur
    // tujuannya otomatis = dapur sendiri, tidak bisa pilih dapur lain. Tetap
    // tersimpan di tabel bahan_pokok yang sama supaya admin logistik bisa
    // melihatnya di Laporan Dapur (halaman Detail Dapur), cuma ditandai kolom
    // "sumber" = dapur.
    public function formBeliMandiri(Request $request)
    {
        $dapur = $request->user()->dapur;
        abort_if(! $dapur, 403, 'Akun ini belum dikaitkan ke dapur manapun. Hubungi Super Admin.');

        $daftarSupplier = Supplier::orderBy('nama_supplier')->get();
        $daftarMasterBarang = MasterBarang::where('jenis', 'bahan_pokok')->orderBy('nama_barang')->get();
        $daftarKategori = KategoriBarang::jenis('bahan_pokok')->orderBy('nama_kategori')->get();

        return view('halaman.dapur_beli_mandiri', compact('dapur', 'daftarSupplier', 'daftarMasterBarang', 'daftarKategori'));
    }

    public function simpanBeliMandiri(Request $request)
    {
        $dapur = $request->user()->dapur;
        abort_if(! $dapur, 403, 'Akun ini belum dikaitkan ke dapur manapun. Hubungi Super Admin.');

        $data = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'bahan' => 'required|array|min:1',
            'bahan.*.master_barang_id' => 'required|exists:master_barang,id',
            'bahan.*.merk' => 'nullable|string|max:100',
            'bahan.*.supplier_id' => 'required|exists:supplier,id',
            'bahan.*.quantity' => 'required|numeric|min:0.01',
            'bahan.*.harga_per_item' => 'required|numeric|min:0',
        ]);

        foreach ($data['bahan'] as $baris) {
            BahanPokok::create([
                'master_barang_id' => $baris['master_barang_id'],
                'merk' => $baris['merk'] ?? null,
                'supplier_id' => $baris['supplier_id'],
                'dapur_id' => $dapur->id,
                'sumber' => 'dapur',
                'tanggal_transaksi' => $data['tanggal_transaksi'],
                'quantity' => $baris['quantity'],
                'harga_per_item' => $baris['harga_per_item'],
            ]);
        }

        $jumlah = count($data['bahan']);

        return redirect()->route('bahanPokok.indeks')
            ->with('success', "{$jumlah} bahan pengganti berhasil dicatat & akan terlihat oleh admin logistik.");
    }
}
