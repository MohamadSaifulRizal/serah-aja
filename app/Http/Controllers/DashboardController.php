<?php

namespace App\Http\Controllers;

use App\Models\BahanPokok;
use App\Models\BarangInventaris;
use App\Models\Dapur;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Dashboard ini KHUSUS admin_logistik (admin_dapur diarahkan ke dapur.beranda sejak login).
    // Tidak ada lagi angka Laba/Produksi - program pemerintah ini tidak berkonsep untung/rugi.
    public function indeks()
    {
        $bulanIni = Carbon::now()->format('Y-m');

        $jumlahDapur = Dapur::count();

        $transaksiBahanBulanIni = BahanPokok::where('tanggal_transaksi', 'like', "$bulanIni%")->get();
        $totalBelanjaBahanBulanIni = $transaksiBahanBulanIni->sum('total_harga');

        $transaksiInventarisBulanIni = BarangInventaris::where('tanggal_beli', 'like', "$bulanIni%")->get();
        $totalBelanjaInventarisBulanIni = $transaksiInventarisBulanIni->sum('total_harga');

        // Transaksi bahan yang sudah dikirim ke dapur tapi belum dilaporkan admin dapur -
        // ini "PR" yang perlu ditindaklanjuti / ditagih laporannya.
        $bahanBelumDilaporkan = BahanPokok::doesntHave('laporan')->with(['dapur', 'masterBarang.kategori'])
            ->orderBy('tanggal_transaksi')->get();

        // Tren belanja bahan pokok 6 bulan terakhir untuk grafik
        $labelBulan = [];
        $dataBelanja = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanX = Carbon::now()->subMonths($i)->format('Y-m');
            $labelBulan[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $dataBelanja[] = (float) BahanPokok::where('tanggal_transaksi', 'like', "$bulanX%")->sum('total_harga');
        }

        return view('halaman.dashboard', compact(
            'jumlahDapur', 'totalBelanjaBahanBulanIni', 'totalBelanjaInventarisBulanIni',
            'bahanBelumDilaporkan', 'labelBulan', 'dataBelanja'
        ));
    }
}
