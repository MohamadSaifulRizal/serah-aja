<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    // Kategori dipisah jadi 2 menu: "Kategori Bahan Pokok" & "Kategori Barang
    // Inventaris". Keduanya memakai controller + view yang sama; bedanya cuma
    // parameter route `jenis` (lihat routes/web.php).
    private function jenisDariRoute(Request $request): string
    {
        $jenis = $request->route('jenis');
        abort_unless(in_array($jenis, [KategoriBarang::JENIS_BAHAN_POKOK, KategoriBarang::JENIS_INVENTARIS], true), 404);

        return $jenis;
    }

    private function namaRoute(string $jenis): string
    {
        return $jenis === KategoriBarang::JENIS_INVENTARIS ? 'kategoriInventaris' : 'kategoriBahanPokok';
    }

    public function indeks(Request $request)
    {
        $jenis = $this->jenisDariRoute($request);

        $daftarKategori = KategoriBarang::jenis($jenis)
            ->withCount('masterBarang')
            ->orderBy('nama_kategori')
            ->get();

        return view('halaman.kategori_indeks', [
            'daftarKategori' => $daftarKategori,
            'jenis' => $jenis,
            'judul' => $jenis === KategoriBarang::JENIS_INVENTARIS ? 'Kategori Barang Inventaris' : 'Kategori Bahan Pokok',
            'namaRoute' => $this->namaRoute($jenis),
            'contohNama' => $jenis === KategoriBarang::JENIS_INVENTARIS ? 'mis. Peralatan Masak, Elektronik, Kebersihan' : 'mis. Sembako, Protein, Sayuran',
        ]);
    }

    public function simpan(Request $request)
    {
        $jenis = $this->jenisDariRoute($request);

        $data = $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('kategori_barang', 'nama_kategori')->where('jenis', $jenis),
            ],
        ]);

        $kategori = KategoriBarang::create($data + ['jenis' => $jenis]);

        // Kalau dipanggil via AJAX (dari popup tambah transaksi/master barang di halaman lain),
        // balas JSON supaya popup asal tidak perlu reload & datanya tidak hilang.
        if ($request->wantsJson()) {
            return response()->json(['kategori' => $kategori]);
        }

        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, KategoriBarang $kategori)
    {
        $this->pastikanJenisCocok($request, $kategori);

        $data = $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('kategori_barang', 'nama_kategori')
                    ->where('jenis', $kategori->jenis)
                    ->ignore($kategori->id),
            ],
        ]);

        $kategori->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function hapus(Request $request, KategoriBarang $kategori)
    {
        $this->pastikanJenisCocok($request, $kategori);

        // withTrashed(): Master Barang yang sudah di-soft-delete tetap menyimpan
        // kategori_id (foreign key), jadi tetap harus dihitung sebagai "dipakai".
        if ($kategori->masterBarang()->withTrashed()->exists()) {
            return back()->withErrors(['hapus' => "Kategori '{$kategori->nama_kategori}' tidak bisa dihapus karena masih dipakai di Master Barang."]);
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    // Mencegah kategori Bahan Pokok diubah/dihapus lewat URL menu Inventaris (dan sebaliknya).
    private function pastikanJenisCocok(Request $request, KategoriBarang $kategori): void
    {
        abort_unless($kategori->jenis === $this->jenisDariRoute($request), 404);
    }
}
