<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function indeks()
    {
        $daftarSupplier = Supplier::withCount('bahanPokok')->orderBy('nama_supplier')->get();

        return view('halaman.supplier_indeks', compact('daftarSupplier'));
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama_supplier' => 'required|string|max:150',
            'kontak_supplier' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($data);

        if ($request->wantsJson()) {
            return response()->json(['supplier' => $supplier]);
        }

        return back()->with('success', 'Supplier baru berhasil ditambahkan!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'nama_supplier' => 'required|string|max:150',
            'kontak_supplier' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
        ]);

        $supplier->update($data);

        return back()->with('success', "{$supplier->nama_supplier} berhasil diperbarui!");
    }

    public function hapus(Supplier $supplier)
    {
        if ($supplier->bahanPokok()->exists()) {
            return back()->withErrors(['hapus' => "{$supplier->nama_supplier} tidak bisa dihapus karena masih punya riwayat transaksi."]);
        }

        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus!');
    }
}
