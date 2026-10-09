<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Biaya yang dikeluarkan saat kondisi barang diubah, diinput manual oleh yang
        // mengupdate. Contoh: rusak_ringan -> baik = biaya perbaikan; rusak_berat ->
        // baru = biaya membeli barang pengganti. Riwayat lama otomatis 0.
        Schema::table('riwayat_kondisi_inventaris', function (Blueprint $table) {
            $table->decimal('biaya', 15, 2)->default(0)->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_kondisi_inventaris', function (Blueprint $table) {
            $table->dropColumn('biaya');
        });
    }
};
