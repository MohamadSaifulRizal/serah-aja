<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Riwayat tiap perubahan kondisi barang inventaris: dari kondisi apa ke
        // kondisi apa, tanggal update, catatan, dan SIAPA yang mengupdate
        // (Admin Logistik / Super Admin / Admin Dapur dari dapur pemilik barang).
        Schema::create('riwayat_kondisi_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_inventaris_id')->constrained('barang_inventaris')->cascadeOnDelete();
            $table->string('kondisi_sebelum', 20)->nullable(); // null = pencatatan kondisi awal
            $table->string('kondisi_baru', 20);
            $table->date('tanggal_update');
            $table->string('catatan')->nullable();
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Ringkasan "update terakhir" disimpan langsung di barangnya supaya tabel
        // bisa menampilkan tanggal & pengupdate tanpa menghitung dari riwayat.
        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->date('kondisi_diperbarui_pada')->nullable()->after('kondisi_barang');
            $table->foreignId('kondisi_diperbarui_oleh')->nullable()->after('kondisi_diperbarui_pada')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kondisi_diperbarui_oleh');
            $table->dropColumn('kondisi_diperbarui_pada');
        });
        Schema::dropIfExists('riwayat_kondisi_inventaris');
    }
};
