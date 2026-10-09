<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Aksi dari admin logistik untuk melacak kondisi tiap barang inventaris per dapur:
        // apakah pernah dirawat/diperbaiki/diganti sparepart, dan berapa biayanya.
        // Biaya WAJIB diisi (bukan untuk hitung laba lagi - program ini tidak ada laba -
        // tapi tetap penting untuk pelacakan biaya operasional per dapur).
        Schema::create('riwayat_perbaikan_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_inventaris_id')->constrained('barang_inventaris')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis_aksi', ['perawatan', 'perbaikan', 'penggantian']);
            $table->decimal('biaya', 15, 2);
            $table->string('vendor')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_perbaikan_inventaris');
    }
};
