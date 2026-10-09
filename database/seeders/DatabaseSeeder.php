<?php

namespace Database\Seeders;

use App\Models\BahanPokok;
use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\KategoriBarang;
use App\Models\LaporanPemakaianBahan;
use App\Models\MasterBarang;
use App\Models\RiwayatPerbaikanInventaris;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data contoh untuk alur bisnis baru (tanpa laba, 2 role).
     * Akun contoh:
     * - superadmin    / rahasia123 (semua menu logistik + Manajemen Akun untuk buat admin logistik & admin dapur)
     * - admin_logistik / rahasia123 (akses semua menu logistik)
     * - admin_dapur1   / rahasia123 (khusus Dapur Sukamaju, lapor pemakaian bahan)
     *
     * REVISI: Bahan Pokok & Barang Inventaris sekarang WAJIB mereferensikan Master
     * Barang lewat foreign key master_barang_id (bukan lagi nama_bahan/kategori_id
     * bebas) - jadi urutan seed-nya HARUS bikin Master Barang dulu sebelum transaksi.
     */
    public function run(): void
    {
        // ===== Kategori =====
        $katSembako = KategoriBarang::create(['nama_kategori' => 'Sembako', 'jenis' => 'bahan_pokok']);
        $katProtein = KategoriBarang::create(['nama_kategori' => 'Protein', 'jenis' => 'bahan_pokok']);
        $katPeralatan = KategoriBarang::create(['nama_kategori' => 'Peralatan Masak', 'jenis' => 'inventaris']);

        // ===== Master Barang (katalog dulu, baru transaksi mereferensikannya) =====
        $mbBeras = MasterBarang::create([
            'nama_barang' => 'Beras', 'jenis' => 'bahan_pokok', 'kategori_id' => $katSembako->id, 'satuan' => 'kg',
        ]);
        $mbAyam = MasterBarang::create([
            'nama_barang' => 'Ayam Fillet', 'jenis' => 'bahan_pokok', 'kategori_id' => $katProtein->id, 'satuan' => 'kg',
        ]);
        $mbKompor = MasterBarang::create([
            'nama_barang' => 'Kompor Gas Industri', 'jenis' => 'inventaris', 'kategori_id' => $katPeralatan->id,
        ]);

        // ===== Dapur =====
        $dapurSukamaju = Dapur::create([
            'nama_dapur' => 'Dapur Sukamaju',
            'alamat' => 'Jl. Merdeka No. 5, Surabaya',
            'penanggung_jawab' => 'Ibu Sari',
        ]);
        $dapurMelati = Dapur::create([
            'nama_dapur' => 'Dapur Melati',
            'alamat' => 'Jl. Melati No. 12, Surabaya',
            'penanggung_jawab' => 'Pak Budi',
        ]);

        // ===== Users (3 role) =====
        User::create([
            'name' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@mbg.local',
            'role' => 'super_admin', 'password' => Hash::make('rahasia123'),
        ]);
        User::create([
            'name' => 'Admin Logistik', 'username' => 'logistik', 'email' => 'logistik@mbg.local',
            'role' => 'admin_logistik', 'password' => Hash::make('rahasia123'),
        ]);
        User::create([
            'name' => 'Admin Dapur Sukamaju', 'username' => 'admin_dapur1', 'email' => 'dapur1@mbg.local',
            'role' => 'admin_dapur', 'dapur_id' => $dapurSukamaju->id, 'password' => Hash::make('rahasia123'),
        ]);

        // ===== Supplier =====
        $supplierPasar = Supplier::create([
            'nama_supplier' => 'Pasar Induk Sukamaju', 'kontak_supplier' => '0812-3456-7890', 'alamat' => 'Pasar Induk Blok C',
        ]);

        // ===== Transaksi Bahan Pokok contoh (referensi ke Master Barang lewat FK) =====
        $transaksiBeras = BahanPokok::create([
            'master_barang_id' => $mbBeras->id, 'merk' => 'Rojolele',
            'supplier_id' => $supplierPasar->id, 'dapur_id' => $dapurSukamaju->id,
            'tanggal_transaksi' => now()->subDays(2)->format('Y-m-d'),
            'quantity' => 50, 'harga_per_item' => 12000,
        ]);
        BahanPokok::create([
            'master_barang_id' => $mbAyam->id, 'merk' => null,
            'supplier_id' => $supplierPasar->id, 'dapur_id' => $dapurMelati->id,
            'tanggal_transaksi' => now()->subDay()->format('Y-m-d'),
            'quantity' => 15, 'harga_per_item' => 20000,
        ]);

        // ===== Contoh laporan pemakaian dari Admin Dapur =====
        LaporanPemakaianBahan::create([
            'bahan_pokok_id' => $transaksiBeras->id,
            'status' => 'sisa',
            'jumlah_sisa' => 8,
            'tanggal_lapor' => now()->format('Y-m-d'),
            'catatan' => 'Masih ada sisa 8kg untuk besok',
        ]);

        // ===== Transaksi Barang Inventaris contoh (referensi ke Master Barang lewat FK) =====
        $kompor = BarangInventaris::create([
            'master_barang_id' => $mbKompor->id,
            'dapur_id' => $dapurSukamaju->id, 'tanggal_beli' => now()->subMonths(2)->format('Y-m-d'),
            'quantity' => 1, 'harga_per_item' => 850000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baik',
        ]);
        RiwayatPerbaikanInventaris::create([
            'barang_inventaris_id' => $kompor->id,
            'tanggal' => now()->subDays(5)->format('Y-m-d'),
            'jenis_aksi' => 'perbaikan', 'biaya' => 150000, 'catatan' => 'Ganti regulator gas bocor',
        ]);
    }
}
