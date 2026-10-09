<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanPokokController;
use App\Http\Controllers\BarangInventarisController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Quick-add dari popup "+" di halaman Tambah Transaksi (Bahan Pokok maupun
    // Beli Mandiri Dapur) - sengaja DILUAR grup role supaya Admin Dapur juga bisa
    // langsung menambah Nama Bahan / Supplier baru tanpa harus ke Master Barang
    // atau Supplier (menu itu sendiri tetap khusus Admin Logistik, cuma endpoint
    // simpan-nya dipakai bareng).
    Route::post('/supplier/simpan', [SupplierController::class, 'simpan'])->name('supplier.simpan');
    Route::post('/master-barang/simpan', [MasterBarangController::class, 'simpan'])->name('masterBarang.simpan');

    // ==== KHUSUS SUPER ADMIN: Manajemen Akun - Admin Logistik & Admin Dapur TIDAK bisa mengakses ====
    Route::middleware('role:super_admin')->prefix('manajemen-akun')->name('akun.')->group(function () {
        Route::get('/', [AkunController::class, 'indeks'])->name('indeks');
        Route::post('/', [AkunController::class, 'simpan'])->name('simpan');
        Route::put('/{akun}', [AkunController::class, 'update'])->name('update');
        Route::delete('/{akun}', [AkunController::class, 'hapus'])->name('hapus');
    });

    // ==== MENU BERSAMA: Admin Logistik, Super Admin & Admin Dapur ====
    // Admin Logistik/Super Admin mengakses SEMUA dapur. Admin Dapur memakai halaman
    // & fitur yang sama, tetapi datanya otomatis DIBATASI ke dapurnya sendiri
    // (dicek di controller lewat Controller::dapurTerbatas()).
    Route::middleware('role:admin_logistik,super_admin,admin_dapur')->group(function () {
        // Kategori dipisah jadi 2 menu sidebar: Kategori Bahan Pokok & Kategori Barang
        // Inventaris. Controller-nya sama, dibedakan lewat parameter `jenis`.
        foreach ([
            'kategoriBahanPokok' => ['kategori-bahan-pokok', 'bahan_pokok'],
            'kategoriInventaris' => ['kategori-barang-inventaris', 'inventaris'],
        ] as $namaRoute => [$uri, $jenis]) {
            Route::prefix($uri)->name($namaRoute.'.')->group(function () use ($jenis) {
                Route::get('/', [KategoriController::class, 'indeks'])->name('indeks')->defaults('jenis', $jenis);
                Route::post('/simpan', [KategoriController::class, 'simpan'])->name('simpan')->defaults('jenis', $jenis);
                Route::put('/{kategori}', [KategoriController::class, 'update'])->name('update')->defaults('jenis', $jenis);
                Route::delete('/{kategori}', [KategoriController::class, 'hapus'])->name('hapus')->defaults('jenis', $jenis);
            });
        }

        // Bahan Pokok: lihat transaksi, detail, export Excel, edit/hapus.
        // (Tambah Transaksi = khusus Admin Logistik; Admin Dapur memakai Pembelian Mandiri.)
        Route::prefix('logistik/bahan-pokok')->name('bahanPokok.')->group(function () {
            Route::get('/', [BahanPokokController::class, 'indeks'])->name('indeks');
            Route::get('/detail', [BahanPokokController::class, 'detailKelompok'])->name('detailKelompok');
            Route::get('/detail/unduh', [BahanPokokController::class, 'exportKelompok'])->name('exportKelompok');
            Route::get('/unduh-semua', [BahanPokokController::class, 'exportSemua'])->name('exportSemua');
            Route::put('/{bahanPokok}', [BahanPokokController::class, 'update'])->name('update');
            Route::delete('/{bahanPokok}', [BahanPokokController::class, 'hapus'])->name('hapus');
        });

        Route::prefix('logistik/barang-inventaris')->name('inventaris.')->group(function () {
            Route::get('/', [BarangInventarisController::class, 'indeks'])->name('indeks');
            Route::get('/tambah', [BarangInventarisController::class, 'formTambah'])->name('formTambah');
            Route::get('/unduh', [BarangInventarisController::class, 'exportExcel'])->name('exportExcel');
            Route::post('/simpan-batch', [BarangInventarisController::class, 'simpanBatch'])->name('simpanBatch');
            Route::put('/{barangInventaris}/kondisi', [BarangInventarisController::class, 'updateKondisi'])->name('updateKondisi');
            Route::put('/{barangInventaris}', [BarangInventarisController::class, 'update'])->name('update');
            Route::delete('/{barangInventaris}', [BarangInventarisController::class, 'hapus'])->name('hapus');
            Route::post('/riwayat', [BarangInventarisController::class, 'simpanRiwayat'])->name('riwayat');
        });
    });

    // ==== KHUSUS ADMIN LOGISTIK & SUPER ADMIN ====
    Route::middleware('role:admin_logistik,super_admin')->group(function () {
        Route::get('/', [DashboardController::class, 'indeks'])->name('dashboard');

        // Master Supplier (endpoint 'simpan' didefinisikan di luar grup ini, dipakai bareng Admin Dapur)
        Route::prefix('supplier')->name('supplier.')->group(function () {
            Route::get('/', [SupplierController::class, 'indeks'])->name('indeks');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{supplier}', [SupplierController::class, 'hapus'])->name('hapus');
        });

        // Master Barang (endpoint 'simpan' didefinisikan di luar grup ini, dipakai bareng Admin Dapur)
        Route::prefix('master-barang')->name('masterBarang.')->group(function () {
            Route::get('/', [MasterBarangController::class, 'indeks'])->name('indeks');
            Route::put('/{masterBarang}', [MasterBarangController::class, 'update'])->name('update');
            Route::delete('/{masterBarang}', [MasterBarangController::class, 'hapus'])->name('hapus');
        });

        // Tambah Transaksi Bahan Pokok (Admin Logistik memilih dapur tujuan)
        Route::prefix('logistik/bahan-pokok')->name('bahanPokok.')->group(function () {
            Route::get('/tambah', [BahanPokokController::class, 'formTambah'])->name('formTambah');
            Route::post('/simpan-batch', [BahanPokokController::class, 'simpanBatch'])->name('simpanBatch');
        });

        // Management Dapur (overview semua dapur - admin dapur pakai dapur.beranda)
        Route::prefix('dapur')->name('dapur.')->group(function () {
            Route::get('/', [DapurController::class, 'indeks'])->name('indeks');
            Route::post('/', [DapurController::class, 'simpan'])->name('simpan');
            Route::put('/{dapur}', [DapurController::class, 'update'])->name('update');
            Route::delete('/{dapur}', [DapurController::class, 'hapus'])->name('hapus');
            Route::get('/{dapur}', [DapurController::class, 'detail'])->name('detail');
        });
    });

    // ==== KHUSUS ADMIN DAPUR ====
    Route::middleware('role:admin_dapur')->group(function () {
        // Dashboard Admin Dapur (diagram ringkasan dapurnya sendiri)
        Route::get('/dapur-saya', [DapurController::class, 'beranda'])->name('dapur.beranda');
        Route::post('/dapur-saya/laporan', [DapurController::class, 'simpanLaporan'])->name('dapur.simpanLaporan');
        // Admin dapur bisa catat pembelian bahan sendiri kapan saja - baik untuk
        // menambah kiriman yang kurang, maupun kebutuhan mendadak lain di dapur.
        // Tetap tercatat di tabel yang sama & terlihat di Laporan Dapur.
        Route::get('/dapur-saya/beli-mandiri', [DapurController::class, 'formBeliMandiri'])->name('dapur.formBeliMandiri');
        Route::post('/dapur-saya/beli-mandiri', [DapurController::class, 'simpanBeliMandiri'])->name('dapur.simpanBeliMandiri');
    });
});
