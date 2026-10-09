<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // PENTING: tabel ini langsung berupa TRANSAKSI (setiap kali admin logistik belanja
        // ke supplier, atau admin dapur beli mandiri = 1 baris baru di sini), BUKAN master
        // barang dengan stok terakumulasi.
        //
        // REVISI: nama_bahan, kategori_id, dan satuan TIDAK LAGI disimpan sebagai teks/kolom
        // bebas di sini - semuanya diwakilkan lewat foreign key master_barang_id ke tabel
        // master_barang (yang sudah menyimpan nama, kategori, dan satuan barang). Ini
        // menghilangkan duplikasi data & mencegah ketidakkonsistenan penulisan nama bahan
        // antar baris transaksi. Merk TETAP kolom sendiri di sini (bukan di master_barang)
        // karena merk yang dibeli bisa beda-beda tiap transaksi walau nama bahannya sama.
        Schema::create('bahan_pokok', function (Blueprint $table) {
            $table->id();
            // restrictOnDelete(): lapisan pengaman tambahan di level database - kalaupun
            // suatu saat ada yang mencoba forceDelete/hard-delete Master Barang secara
            // langsung (bukan lewat aplikasi, yang sebenarnya cuma soft delete), transaksi
            // lama yang masih mereferensikannya TIDAK akan ikut hilang/rusak; database akan
            // menolak proses hapus itu selama masih ada transaksi yang memakainya.
            $table->foreignId('master_barang_id')->constrained('master_barang')->restrictOnDelete();
            $table->string('merk')->nullable();
            $table->foreignId('supplier_id')->constrained('supplier');
            $table->foreignId('dapur_id')->constrained('dapur'); // dapur tujuan pengiriman
            // Menandai asal transaksi: dibuat oleh Admin Logistik (alur normal) atau dibuat
            // MANDIRI oleh Admin Dapur sendiri (kiriman kurang / kebutuhan mendadak lain).
            $table->enum('sumber', ['logistik', 'dapur'])->default('logistik');
            $table->date('tanggal_transaksi');
            $table->decimal('quantity', 12, 2);
            $table->decimal('harga_per_item', 15, 2);
            $table->decimal('total_harga', 15, 2); // = quantity * harga_per_item, dihitung di model
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_pokok');
    }
};
