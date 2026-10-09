<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fitur Pengeluaran Operasional dihapus, digantikan Manajemen Akun.
        Schema::dropIfExists('pengeluaran_operasional');
    }

    public function down(): void
    {
        Schema::create('pengeluaran_operasional', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kategori');
            $table->string('deskripsi')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });
    }
};
