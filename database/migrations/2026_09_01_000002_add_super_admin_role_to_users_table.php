<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Kolom role sebelumnya ENUM('admin_logistik','admin_dapur'). Sekarang ada 3 role:
     * super_admin, admin_logistik, admin_dapur. Kolom diubah jadi string biasa
     * (validasi role dilakukan di aplikasi) supaya aman di SQLite/MySQL/PostgreSQL.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // ENUM Laravel di PostgreSQL = varchar + CHECK constraint -> constraint dibuang.
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('admin_logistik')->change();
        });
    }

    public function down(): void
    {
        // Akun super_admin tidak punya padanan di enum lama -> jadikan admin_logistik.
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin_logistik']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin_logistik', 'admin_dapur'])->default('admin_logistik')->change();
        });
    }
};
