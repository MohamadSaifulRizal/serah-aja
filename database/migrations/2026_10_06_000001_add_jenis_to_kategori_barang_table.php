<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Kategori sekarang DIPISAH jadi 2 menu: "Kategori Bahan Pokok" dan
        // "Kategori Barang Inventaris". Pemisahnya kolom `jenis` ini.
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->enum('jenis', ['bahan_pokok', 'inventaris'])->default('bahan_pokok')->after('nama_kategori');
        });

        // Dulu nama_kategori unik secara global. Sekarang cukup unik DI DALAM jenisnya
        // (boleh ada "Peralatan" di Bahan Pokok dan "Peralatan" di Inventaris).
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropUnique(['nama_kategori']);
        });
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->unique(['jenis', 'nama_kategori']);
        });

        // ==== Migrasi data lama (aman dijalankan di database yang sudah berisi) ====
        // Kategori lama dipakai bersama. Tiap kategori diputuskan masuk ke menu mana
        // berdasarkan Master Barang yang memakainya:
        //  - hanya dipakai inventaris            -> pindah jadi jenis inventaris
        //  - dipakai bahan_pokok & inventaris    -> DIPECAH: kategori asli tetap untuk
        //                                           bahan pokok, dibuatkan salinan untuk
        //                                           inventaris & Master Barang inventaris
        //                                           dipindahkan ke salinan itu
        //  - hanya bahan pokok / belum dipakai   -> tetap bahan_pokok (default)
        foreach (DB::table('kategori_barang')->get() as $kategori) {
            $dipakai = DB::table('master_barang')
                ->where('kategori_id', $kategori->id)
                ->distinct()
                ->pluck('jenis')
                ->all();

            $pakaiBahan = in_array('bahan_pokok', $dipakai, true);
            $pakaiInventaris = in_array('inventaris', $dipakai, true);

            if ($pakaiInventaris && ! $pakaiBahan) {
                DB::table('kategori_barang')->where('id', $kategori->id)->update(['jenis' => 'inventaris']);
            } elseif ($pakaiInventaris && $pakaiBahan) {
                $idBaru = DB::table('kategori_barang')->insertGetId([
                    'nama_kategori' => $kategori->nama_kategori,
                    'jenis' => 'inventaris',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('master_barang')
                    ->where('kategori_id', $kategori->id)
                    ->where('jenis', 'inventaris')
                    ->update(['kategori_id' => $idBaru]);
            }
        }
    }

    public function down(): void
    {
        // Sebelum kembali ke aturan lama (nama kategori unik global), nama kembar
        // antar jenis dibedakan dulu supaya unique index bisa dibuat ulang.
        $namaBahanPokok = DB::table('kategori_barang')->where('jenis', 'bahan_pokok')->pluck('nama_kategori')->all();
        foreach (DB::table('kategori_barang')->where('jenis', 'inventaris')->get() as $kategori) {
            if (in_array($kategori->nama_kategori, $namaBahanPokok, true)) {
                DB::table('kategori_barang')->where('id', $kategori->id)
                    ->update(['nama_kategori' => $kategori->nama_kategori.' (Inventaris)']);
            }
        }

        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropUnique(['jenis', 'nama_kategori']);
        });
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->unique('nama_kategori');
        });
    }
};
