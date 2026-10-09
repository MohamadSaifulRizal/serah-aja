<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Sama seperti bahan_pokok: langsung berupa TRANSAKSI (tiap pembelian/penambahan
        // aset ke dapur = 1 baris baru). status_barang membedakan apakah barang ini
        // consumable (habis pakai, mis. sabun cuci piring) atau aset tetap (mis. kompor).
        //
        // REVISI: nama_barang & kategori_id tidak lagi disimpan bebas di sini - diwakilkan
        // lewat foreign key master_barang_id (sama pola dengan bahan_pokok). Barang
        // Inventaris tidak punya kolom satuan (memang tidak pernah punya - dihitung per
        // unit, bukan per kg/liter).
        Schema::create('barang_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_barang_id')->constrained('master_barang')->restrictOnDelete();
            $table->string('merk')->nullable();
            $table->foreignId('dapur_id')->constrained('dapur'); // milik dapur mana
            $table->date('tanggal_beli');
            $table->decimal('quantity', 12, 2);
            $table->decimal('harga_per_item', 15, 2);
            $table->decimal('total_harga', 15, 2); // = quantity * harga_per_item
            $table->enum('status_barang', ['barang_habis_pakai', 'barang_tetap']);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_inventaris');
    }
};
