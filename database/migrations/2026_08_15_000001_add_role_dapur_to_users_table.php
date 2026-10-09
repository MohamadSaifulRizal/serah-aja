<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 2 peran: admin_logistik (kelola bahan/inventaris/supplier/kategori) dan
        // admin_dapur (lapor pemakaian bahan & kondisi inventaris di dapurnya sendiri).
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin_logistik', 'admin_dapur'])->default('admin_logistik')->after('email');
            // Hanya diisi kalau role = admin_dapur -> menentukan dapur mana yang dia kelola.
            $table->foreignId('dapur_id')->nullable()->after('role')->constrained('dapur')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dapur_id');
            $table->dropColumn('role');
        });
    }
};
