<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Kondisi fisik barang inventaris. Data lama otomatis dianggap "baik".
        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->enum('kondisi_barang', ['baru', 'baik', 'rusak_ringan', 'rusak_berat'])
                ->default('baik')
                ->after('status_barang');
        });
    }

    public function down(): void
    {
        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->dropColumn('kondisi_barang');
        });
    }
};
