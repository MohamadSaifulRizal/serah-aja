<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterBarangController extends Controller
{
    // Master Barang: katalog nama bahan supaya bisa dipilih langsung saat
    // "Tambah Transaksi" di Bahan Pokok (tidak perlu ketik ulang tiap kali).
    public function indeks()
    {
        $daftarBarang = MasterBarang::with('kategori')->orderBy('nama_barang')->get();
        $daftarKategori = KategoriBarang::orderBy('nama_kategori')->get();

        return view('halaman.master_barang_indeks', compact('daftarBarang', 'daftarKategori'));
    }

    // Kategori sekarang terpisah per jenis, jadi kategori yang dipilih HARUS
    // berjenis sama dengan jenis Master Barang-nya (Bahan Pokok <-> Kategori
    // Bahan Pokok, Inventaris <-> Kategori Barang Inventaris).
    private function validasi(Request $request): array
    {
        $jenis = $request->input('jenis');

        return $request->validate([
            'jenis' => 'required|in:bahan_pokok,inventaris',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => [
                'required',
                Rule::exists('kategori_barang', 'id')->where('jenis', $jenis),
            ],
            'satuan' => 'nullable|string|max:30',
        ], [
            'kategori_id.exists' => 'Kategori yang dipilih tidak sesuai dengan jenis barang (Bahan Pokok / Inventaris).',
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $this->validasi($request);

        // Inventaris tidak punya satuan (mis. kompor, kulkas dihitung per unit,
        // bukan per kg/liter), jadi dikosongkan walau sempat terisi dari form.
        if ($data['jenis'] === 'inventaris') {
            $data['satuan'] = null;
        }

        $masterBarang = MasterBarang::create($data);

        // Dipakai juga sebagai quick-add via AJAX dari popup "+" di halaman Tambah
        // Transaksi (Bahan Pokok, termasuk punya Admin Dapur) - balas JSON supaya
        // popup asal tidak perlu reload & data yang sudah diisi tidak hilang.
        if ($request->wantsJson()) {
            return response()->json(['masterBarang' => $masterBarang->load('kategori')]);
        }

        return back()->with('success', 'Barang baru berhasil ditambahkan ke Master Barang!');
    }

    public function update(Request $request, MasterBarang $masterBarang)
    {
        $data = $this->validasi($request);

        if ($data['jenis'] === 'inventaris') {
            $data['satuan'] = null;
        }

        $masterBarang->update($data);

        return back()->with('success', 'Master Barang berhasil diperbarui!');
    }

    public function hapus(MasterBarang $masterBarang)
    {
        // Soft delete (lihat trait SoftDeletes di model MasterBarang) - barang ini
        // hilang dari katalog aktif & dropdown Tambah Transaksi, tapi baris datanya
        // tetap ada di database supaya histori transaksi lama yang sudah memakainya
        // tidak rusak/hilang.
        $masterBarang->delete();

        return back()->with('success', 'Master Barang berhasil dihapus dari katalog! (Riwayat transaksi yang sudah memakainya tetap aman.)');
    }
}
