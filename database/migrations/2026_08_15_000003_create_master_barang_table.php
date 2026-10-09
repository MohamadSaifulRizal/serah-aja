<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Master Barang: katalog nama bahan/barang yang sudah pernah dipakai, supaya
        // saat "Tambah Transaksi" (Bahan Pokok maupun Barang Inventaris) tinggal
        // pilih dari daftar ini (otomatis terisi kategori & satuan) tanpa perlu
        // ketik ulang dari nol setiap kali. Bahan Pokok & Barang Inventaris sekarang
        // BENAR-BENAR mereferensikan tabel ini lewat foreign key master_barang_id
        // (lihat migration bahan_pokok & barang_inventaris) - jadi kategori & nama
        // barang tidak lagi disimpan dobel sebagai teks bebas di tiap transaksi.
        Schema::create('master_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            // Membedakan 2 jenis input: Bahan Pokok (punya satuan) dan Inventaris
            // (tanpa satuan) - dipilih PERTAMA KALI di form Tambah Master Barang,
            // baru field lain menyesuaikan. Dipakai juga untuk memfilter dropdown
            // "Nama Bahan"/"Nama Barang" di halaman Tambah Transaksi masing-masing.
            $table->enum('jenis', ['bahan_pokok', 'inventaris'])->default('bahan_pokok');
            // Merk SENGAJA TIDAK ADA di sini - merk baru diisi nanti per transaksi (di Bahan
            // Pokok / Barang Inventaris), karena merk yang dibeli bisa beda-beda tiap hari.
            $table->foreignId('kategori_id')->constrained('kategori_barang');
            $table->string('satuan')->nullable(); // kg, liter, pcs, dst (kosong untuk jenis inventaris)
            $table->timestamps();
            // PENTING (soft delete): kalau Master Barang "dihapus" dari menunya, baris
            // ini TIDAK betul-betul dibuang dari database - cuma ditandai deleted_at.
            // Tujuannya supaya foreign key master_barang_id di histori transaksi lama
            // (Bahan Pokok / Barang Inventaris) tetap valid dan tetap bisa menampilkan
            // nama/kategori/satuan aslinya, walaupun barangnya sudah "dihapus" dari
            // katalog aktif (tidak muncul lagi di dropdown Tambah Transaksi yang baru).
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_barang');
    }
};
