<?php

namespace App\Support;

use App\Models\BarangInventaris;
use App\Models\Dapur;
use Illuminate\Support\Str;

/**
 * Pembuat file Excel data Barang Inventaris. Dipakai bareng oleh sisi Logistik
 * (semua dapur / dapur tertentu) dan sisi Admin Dapur (hanya dapurnya sendiri).
 */
class LaporanInventaris
{
    // $dapur = null  -> semua dapur; selain itu hanya barang milik dapur tersebut.
    public static function unduh(?Dapur $dapur)
    {
        $query = BarangInventaris::with([
            'masterBarang.kategori', 'dapur', 'riwayatPerbaikan', 'pembaruKondisi', 'riwayatKondisi.pengguna',
        ]);
        if ($dapur) {
            $query->where('dapur_id', $dapur->id);
        }

        $daftar = $query->get()
            ->sortBy([
                fn ($a, $b) => strcasecmp($a->dapur->nama_dapur ?? '', $b->dapur->nama_dapur ?? ''),
                fn ($a, $b) => $b->tanggal_beli <=> $a->tanggal_beli,
            ])
            ->values();

        $labelDapur = $dapur ? $dapur->nama_dapur : 'Semua Dapur';

        $x = new XlsxWriter();
        $s = $x->sheet('Barang Inventaris');
        $s->judul('Data Barang Inventaris - '.$labelDapur)
            ->meta('Diekspor pada '.now()->translatedFormat('d F Y H:i').' | Jumlah data: '.$daftar->count().' baris')
            ->kosong()
            ->header([
                'No', 'Dapur Pemilik', 'Nama Barang', 'Merk', 'Kategori', 'Tanggal Beli', 'Qty', 'Harga per Item',
                'Total Harga', 'Status Barang', 'Kondisi Barang', 'Tanggal Update Kondisi', 'Diupdate Oleh',
                'Total Biaya Perawatan', 'Total Biaya Update Kondisi', 'Catatan',
            ]);

        $tipe = ['tengah', 'teks', 'teks', 'teks', 'teks', 'tanggal', 'angka', 'uang', 'uang', 'teks', 'teks', 'tanggal', 'teks', 'uang', 'uang', 'teks'];
        foreach ($daftar as $i => $b) {
            $s->baris([
                $i + 1,
                $b->dapur->nama_dapur ?? '-',
                $b->namaBarang(),
                $b->merk ?: '-',
                $b->namaKategori(),
                $b->tanggal_beli,
                (float) $b->quantity,
                (float) $b->harga_per_item,
                (float) $b->total_harga,
                $b->labelStatusBarang(),
                $b->labelKondisiBarang(),
                $b->kondisi_diperbarui_pada,
                $b->kondisi_diperbarui_pada ? ($b->pembaruKondisi?->namaDanUsername() ?? '(akun sudah dihapus)') : '-',
                (float) $b->riwayatPerbaikan->sum('biaya'),
                $b->totalBiayaUpdateKondisi(),
                $b->catatan ?: '-',
            ], $tipe);
        }
        $s->barisTotal(16, 7, 'Total Keseluruhan', 8, (float) $daftar->sum('total_harga'));
        $s->lebarKolom([6, 24, 30, 16, 20, 14, 8, 16, 16, 20, 16, 18, 22, 20, 22, 30]);

        // Sheet riwayat: setiap perubahan kondisi beserta siapa yang mengupdate.
        $h = $x->sheet('Riwayat Kondisi');
        $h->judul('Riwayat Perubahan Kondisi Barang Inventaris - '.$labelDapur)
            ->kosong()
            ->header(['No', 'Tanggal Update', 'Dapur Pemilik', 'Nama Barang', 'Kondisi Sebelum', 'Kondisi Baru', 'Biaya Update Kondisi', 'Diupdate Oleh', 'Catatan']);

        $semuaRiwayat = $daftar->flatMap(fn ($b) => $b->riwayatKondisi->map(fn ($r) => [$b, $r]))
            ->sortByDesc(fn ($p) => $p[1]->tanggal_update->format('Y-m-d').str_pad((string) $p[1]->id, 10, '0', STR_PAD_LEFT))
            ->values();

        foreach ($semuaRiwayat as $i => [$b, $r]) {
            $h->baris([
                $i + 1,
                $r->tanggal_update,
                $b->dapur->nama_dapur ?? '-',
                $b->namaBarang(),
                $r->kondisi_sebelum ? BarangInventaris::labelKondisi($r->kondisi_sebelum) : '(kondisi awal)',
                BarangInventaris::labelKondisi($r->kondisi_baru),
                (float) $r->biaya,
                $r->namaPengguna(),
                $r->catatan ?: '-',
            ], ['tengah', 'tanggal', 'teks', 'teks', 'teks', 'teks', 'uang', 'teks', 'teks']);
        }
        $h->barisTotal(9, 5, 'Total Biaya Update Kondisi', 6, (float) $semuaRiwayat->sum(fn ($p) => $p[1]->biaya));
        $h->lebarKolom([6, 16, 24, 30, 18, 16, 22, 22, 36]);

        // Kalau export SEMUA dapur, tambah sheet rekap kondisi barang per dapur.
        if (! $dapur) {
            $r = $x->sheet('Rekap Kondisi per Dapur');
            $r->judul('Rekap Kondisi Barang Inventaris per Dapur (jumlah unit)')
                ->kosong()
                ->header(['No', 'Dapur Pemilik', 'Baru', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Total Unit', 'Total Harga']);

            $no = 1;
            foreach ($daftar->groupBy('dapur_id') as $items) {
                $per = fn (string $kondisi) => (float) $items->where('kondisi_barang', $kondisi)->sum('quantity');
                $r->baris([
                    $no++,
                    $items->first()->dapur->nama_dapur ?? '-',
                    $per('baru'), $per('baik'), $per('rusak_ringan'), $per('rusak_berat'),
                    (float) $items->sum('quantity'),
                    (float) $items->sum('total_harga'),
                ], ['tengah', 'teks', 'angka', 'angka', 'angka', 'angka', 'angka', 'uang']);
            }
            $r->lebarKolom([6, 28, 10, 10, 14, 14, 12, 18]);
        }

        $bagianNama = $dapur ? Str::slug($dapur->nama_dapur) : 'semua-dapur';

        return $x->unduh('barang-inventaris-'.$bagianNama.'-'.now()->format('Ymd').'.xlsx');
    }
}
