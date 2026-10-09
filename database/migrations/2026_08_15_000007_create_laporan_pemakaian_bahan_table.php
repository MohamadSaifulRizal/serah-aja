<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ini jawaban dari kebutuhan "laporan apakah bahan yang dibeli sisa berapa,
        // digunakan semua, atau malah kurang" - diisi oleh ADMIN DAPUR (bukan admin logistik),
        // merujuk ke 1 transaksi bahan_pokok yang dikirim ke dapurnya.
        Schema::create('laporan_pemakaian_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_pokok_id')->constrained('bahan_pokok')->onDelete('cascade');
            $table->enum('status', ['habis', 'sisa', 'kurang']);
            $table->decimal('jumlah_sisa', 12, 2)->nullable();   // diisi kalau status = sisa
            $table->decimal('jumlah_kurang', 12, 2)->nullable(); // diisi kalau status = kurang
            $table->date('tanggal_lapor');
            $table->string('catatan')->nullable();
            $table->foreignId('dilaporkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_pemakaian_bahan');
    }
};
