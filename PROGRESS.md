# Progress Revisi Besar — Aplikasi Logistik Dapur MBG (Tanpa Laba, 2 Role)

## Ringkasan Perubahan Bisnis yang Disepakati
- **Laba dihapus total** (program pemerintah, tidak ada konsep untung/rugi).
- **2 role login**: `admin_logistik` (kelola bahan/inventaris/supplier/kategori) dan
  `admin_dapur` (lapor pemakaian bahan & kondisi inventaris, khusus dapurnya sendiri).
- Alur bisnis: Dapur minta bahan ke Admin Logistik -> Admin Logistik belanja ke
  Supplier (dicatat sebagai transaksi `bahan_pokok`) -> dikirim ke dapur tujuan ->
  Admin Dapur lapor apakah bahan itu habis / masih sisa / malah kurang.
- Tabel `barang` (gabungan lama) dipecah jadi 2 tabel **transaksi langsung**:
  `bahan_pokok` dan `barang_inventaris` (setiap kali tambah = 1 baris transaksi baru,
  bukan lagi master+stok akumulasi).
- **Master Kategori** jadi menu tersendiri (pakai tabel `kategori_barang` yang sudah
  ada), relasi wajib ke `bahan_pokok` maupun `barang_inventaris`.
- **Master Barang** tetap ada tapi jadi menu terpisah (bukan di dalam dropdown
  Logistik lagi) - fungsinya sebagai katalog referensi nama barang.
- **Supplier** jadi tabel baru, terhubung ke `bahan_pokok` (dipilih saat transaksi).
- Menu **Logistik** dropdown-nya disederhanakan jadi 2: Bahan Pokok, Barang
  Inventaris - keduanya langsung menampilkan/input transaksi.
- Menu **Dapur** berubah total: bukan lagi kalkulator laba, tapi laporan status
  bahan per transaksi (habis/sisa/kurang), diisi oleh Admin Dapur.

## FASE A — Skema Database (Migration): SELESAI ✅
Lokasi: `database/migrations/`

**Migration lama yang DIHAPUS** (karena skema fundamental berubah, tidak bisa
sekadar di-alter): `create_barang_table`, `create_mutasi_stok_table`,
`create_riwayat_inventaris_table` (lama), `create_produksi_table`,
`create_produksi_bahan_table`, `add_produksi_id_to_mutasi_stok_table`,
`add_dapur_id_to_barang_table`, `update_barang_kategori`,
`update_riwayat_inventaris`, `rename_stok_minimum_ke_target`.

**Migration baru:**
1. `add_role_dapur_to_users_table` - kolom `role` (enum admin_logistik/admin_dapur)
   dan `dapur_id` (nullable, dipakai kalau role = admin_dapur).
2. `create_supplier_table` - id, nama_supplier, kontak_supplier, alamat.
3. `create_master_barang_table` - id, nama_barang, jenis (bahan_pokok/inventaris),
   kategori_id (wajib), merk.
4. `create_bahan_pokok_table` - **transaksi**, kolom sesuai permintaan (nama_bahan,
   merk, quantity, harga_per_item, total_harga) **+ tambahan yang saya anggap perlu
   secara logika bisnis**: kategori_id (wajib), supplier_id (wajib), dapur_id
   (wajib, tujuan pengiriman), tanggal_transaksi, satuan, catatan.
5. `create_barang_inventaris_table` - **transaksi**, kolom sesuai permintaan
   (nama_barang, merk, tanggal_beli, quantity, harga_per_item, status_barang)
   **+ tambahan**: kategori_id (wajib), dapur_id (wajib, pemilik), total_harga
   (dihitung otomatis), catatan.
6. `create_riwayat_perbaikan_inventaris_table` - aksi dari admin logistik untuk
   tiap barang inventaris (perawatan/perbaikan/penggantian), biaya WAJIB diisi
   (bukan untuk hitung laba lagi, tapi tetap penting untuk pelacakan biaya per dapur).
7. `create_laporan_pemakaian_bahan_table` - diisi ADMIN DAPUR, merujuk 1 transaksi
   `bahan_pokok`, status habis/sisa/kurang + jumlah terkait.

**Tabel yang DIPERTAHANKAN tanpa perubahan:** `kategori_barang`, `dapur`,
`pengeluaran_operasional` (masih berdiri sendiri, lihat pertanyaan terbuka di bawah).

## PERTANYAAN TERBUKA — Mohon Dikonfirmasi Sebelum Fase B
Beberapa hal tidak disebutkan detail kolomnya, jadi saya ambil keputusan sendiri.
Tolong dikoreksi kalau salah:

1. **`dapur_id` di `bahan_pokok` & `barang_inventaris` saya buat WAJIB** (setiap
   transaksi harus langsung punya dapur tujuan). Kalau ternyata admin logistik
   perlu belanja dulu ke "gudang pusat" tanpa tujuan dapur yang jelas di awal
   (baru dialokasikan ke dapur belakangan), kolom ini seharusnya nullable dan
   perlu ada langkah "alokasi ke dapur" terpisah. Yang mana yang benar? boleh belum ditentukan
2. **Permintaan bahan dari Dapur ke Admin Logistik** ("dapur ngasih tau logistik
   suruh beli apa") - ini belum saya buatkan tabelnya sama sekali, saya asumsikan
   itu masih terjadi di luar aplikasi (lewat WA/lisan). Apakah perlu dibuatkan
   fitur "Permintaan Bahan" formal di dalam aplikasi juga, atau memang cukup
   di luar sistem untuk sekarang? cukup diluar aja
3. **`pengeluaran_operasional`** (biaya operasional umum, dulu dipakai buat
   pengurang Laba) - sekarang Laba dihapus, jadi fitur ini saya PERTAHANKAN
   strukturnya tapi belum saya tentukan mau ditaruh di menu mana. Masih perlu
   dipakai, atau boleh dihapus juga? untuk catatan mentenance itu masih di pertahankan dulu
4. **`total_harga`** di kedua tabel transaksi saya buat dihitung OTOMATIS
   (quantity x harga_per_item), bukan diinput manual - supaya konsisten. Setuju? setuju

## FASE B — Models: SELESAI ✅
Lokasi: `app/Models/`

Model baru: `Supplier`, `MasterBarang`, `BahanPokok` (auto-hitung `total_harga`
via `saving` event, relasi kategori/supplier/dapur/laporan), `BarangInventaris`
(auto-hitung `total_harga`, relasi riwayatPerbaikan, method
`totalBiayaPerawatan()`), `RiwayatPerbaikanInventaris`, `LaporanPemakaianBahan`.

Model dirombak total: `User` (tambah `role`, `dapur_id`, helper
`isAdminLogistik()`/`isAdminDapur()`), `Dapur` (semua method Laba/Produksi lama
dibuang, diganti `bahanPokok()`, `barangInventaris()`,
`totalBiayaPerawatanInventaris()`, `bahanPokokBelumDilaporkan()` - ini yang nanti
dipakai buat "PR" admin dapur di dashboard-nya), `KategoriBarang` (relasi ke 3
tabel baru: bahanPokok/barangInventaris/masterBarang).

Model DIHAPUS: `Barang`, `MutasiStok`, `RiwayatInventaris` (lama), `Produksi`,
`ProduksiBahan`.

Seeder (`DatabaseSeeder.php`) disesuaikan total: 2 akun contoh
(`logistik`/`admin_dapur1`, password `rahasia123`), data kategori, dapur,
supplier, master barang, transaksi bahan pokok + 1 contoh sudah dilaporkan admin
dapur (status sisa), transaksi barang inventaris + 1 riwayat perbaikan.

**Asumsi yang dipakai (belum dikonfirmasi user):** semua 4 pertanyaan terbuka
dari Fase A dijalankan sesuai default yang saya usulkan (dapur_id wajib,
permintaan bahan di luar sistem dulu, pengeluaran_operasional dipertahankan tapi
belum ditentukan menunya, total_harga otomatis).

## FASE C — Auth & Middleware Role: SELESAI ✅
- `app/Http/Middleware/CekRole.php` dibuat: dipakai sebagai `->middleware('role:admin_logistik')`
  atau `->middleware('role:admin_dapur')` di routes. Kalau role tidak cocok, user
  diarahkan ke dashboard sesuai haknya sendiri (bukan halaman error 403 polos).
- Didaftarkan sebagai alias `role` di `bootstrap/app.php`.
- `AuthController::login()` sekarang redirect beda tergantung role: admin_logistik
  ke `dashboard`, admin_dapur langsung ke `dapur.beranda` (halaman laporan
  pemakaian bahan khusus dapurnya sendiri - dia tidak perlu lihat dashboard umum).

**CATATAN PENTING:** middleware ini mereferensikan route bernama `dapur.beranda`
yang BELUM ada (baru dibuat di Fase D). Jangan coba jalankan aplikasi dulu
sebelum Fase D selesai, nanti error "Route [dapur.beranda] not defined".

**Belum diputuskan di fase ini (lanjut ke Fase D):** bagaimana caranya admin_dapur
yang login cuma bisa lihat/lapor data DAPURNYA SENDIRI (bukan dapur lain) - ini
perlu dicek di level Controller (filter query by `$request->user()->dapur_id`),
bukan cuma middleware role biasa.

## FASE D — Controllers + Routes: SELESAI ✅
Controller baru: `SupplierController`, `KategoriController` (menu Master Kategori
berdiri sendiri, plus dukungan respons JSON untuk quick-add via AJAX),
`MasterBarangController` (dirombak total jadi katalog referensi, menu terpisah),
`BahanPokokController` (transaksi langsung: simpan/update/hapus, filter by
dapur_id), `BarangInventarisController` (transaksi + `simpanRiwayat()` untuk
perawatan/perbaikan/penggantian), `DapurController` (dirombak total: `indeks()`
+ `detail()` untuk admin_logistik lihat overview semua dapur, `beranda()` +
`simpanLaporan()` khusus admin_dapur - ada pengecekan keamanan supaya admin dapur
CUMA bisa lapor bahan yang benar dikirim ke dapurnya sendiri, bukan dapur lain),
`DashboardController` (dirombak total, tanpa data Laba/Produksi - sekarang isinya
ringkasan belanja bulan ini + daftar transaksi bahan yang belum dilaporkan admin
dapur + tren 6 bulan).

Controller DIHAPUS: `BarangInventarisController` (versi lama), `BarangOlahanController`,
`LabaController`, `MasterBarangController` (versi lama), `ProduksiController`.

`app/Helpers/KalkulatorDapur.php` dibersihkan: method `hitungBukuStok()` (kartu
stok manual berbasis mutasi_stok) dibuang karena sudah tidak relevan - sekarang
formatter angka/rupiah saja yang tersisa, masih dipakai di semua view.

`routes/web.php` ditulis ulang total: dipisah 2 grup middleware role
(`role:admin_logistik` dan `role:admin_dapur`). Menu Pengeluaran Operasional
(pertanyaan terbuka #3) saya putuskan tetap dipertahankan dengan halaman sendiri
(`operasional.indeks`), bukan digabung ke Dashboard - supaya lebih rapi dan
gampang dicari.

**Yang WAJIB dikerjakan di Fase E supaya bisa dites:** semua view di
`resources/views/halaman/` untuk controller-controller baru ini BELUM ada
(kecuali dashboard/login lama yang strukturnya sudah tidak cocok lagi dengan data
baru). Sidebar juga masih pakai menu lama.

## FASE E — Views: SELESAI ✅
Lokasi: `resources/views/`

Sidebar (`komponen/sidebar.blade.php`) dirombak total, sekarang **role-aware**:
admin_dapur cuma lihat 1 menu ("Dapur Saya"), admin_logistik lihat semua menu
(Dashboard, Master Kategori, Master Barang, Supplier, Logistik dropdown, Dapur,
Pengeluaran Operasional).

Halaman baru: `dashboard.blade.php` (tanpa data Laba), `kategori_indeks.blade.php`,
`master_barang.blade.php` (katalog + quick-add kategori via AJAX, pelajaran dari
bug sebelumnya diterapkan: SEMUA string/komentar JS di dalam `x-data` pakai kutip
satu, sudah di-cek ulang programatis - aman), `supplier_indeks.blade.php`,
`bahan_pokok.blade.php` (paling kompleks: quick-add kategori DAN supplier
langsung dari form tambah transaksi, tanpa kehilangan isian form),
`barang_inventaris.blade.php` (transaksi + riwayat perawatan/perbaikan/
penggantian, biaya wajib diisi), `dapur_indeks.blade.php` + `dapur_detail.blade.php`
(overview admin logistik), `dapur_beranda.blade.php` (halaman inti admin dapur:
kartu "Perlu Dilaporkan" + form lapor habis/sisa/kurang + riwayat laporan),
`pengeluaran_operasional.blade.php`.

Semua file baru sudah dicek terprogram: keseimbangan tag `<div>`/`<form>`, dan
tidak ada tanda kutip dua literal di dalam atribut `x-data` manapun (pelajaran
dari bug "popup tidak muncul" sebelumnya).

View lama yang DIHAPUS: `barang_inventaris.blade.php` (lama), `barang_olahan.blade.php`,
`dapur_detail.blade.php` (lama, berbasis laba), `dapur_indeks.blade.php` (lama),
`dashboard.blade.php` (lama), `laba_indeks.blade.php`, `master_barang.blade.php`
(lama), `produksi_buat.blade.php`, `produksi_detail.blade.php`.

Layout utama (`skema/aplikasi.blade.php`) TIDAK diubah - sudah responsive dari
perbaikan sebelumnya (hamburger menu mobile, sidebar toggle) dan tetap kompatibel.

**Belum dibereskan (boleh nanti, tidak mendesak):** `komponen/badge-stok.blade.php`
dan `komponen/badge-status.blade.php` sekarang jadi file mati (tidak dipakai lagi
karena konsep "status stok" sudah tidak ada di skema transaksi baru) - aman
dibiarkan, tidak mengganggu.

## FASE F — Testing & Polish: BELUM DIKERJAKAN
Karena sandbox Claude tidak punya PHP, belum pernah dites `php artisan serve`
sungguhan. Checklist yang perlu dicoba manual di lokal:
1. `composer install`
2. `cp .env.example .env && php artisan key:generate`
3. `touch database/database.sqlite` (kalau pakai SQLite)
4. `php artisan migrate:fresh --seed` (WAJIB fresh, skema berubah total)
5. `php artisan serve`
6. Login sebagai `logistik` / `rahasia123` -> cek semua menu admin logistik jalan
7. Logout, login sebagai `admin_dapur1` / `rahasia123` -> pastikan HANYA lihat
   menu "Dapur Saya", coba submit laporan pemakaian bahan
8. Coba quick-add kategori & supplier dari popup Tambah Transaksi Bahan Pokok -
   pastikan popup tambah transaksi TIDAK hilang & data yang sudah diisi tetap ada
9. Coba hapus kategori yang masih dipakai barang - harus ditolak dengan pesan
   error, bukan crash
10. Cek tampilan di HP (responsive) - sidebar & tabel transaksi yang lebar

Kalau ada error saat mencoba, laporkan pesan errornya lengkap (termasuk file &
baris kalau ada) supaya bisa diperbaiki dengan tepat.

---
### PENTING SEBELUM DICOBA
Skema database berubah TOTAL (bukan sekadar tambah kolom), jadi WAJIB pakai:
```
php artisan migrate:fresh --seed
```
Data lama di database lokal akan hilang semua - tidak ada jalan lain karena
struktur tabelnya memang berbeda total.

### Cara Melanjutkan di Sesi Baru
Upload lagi zip terbaru dari /mnt/user-data/outputs, lalu bilang ke Claude:
**"lanjutkan sesuai PROGRESS.md, mulai dari Fase B"** (dan jawab 4 pertanyaan
terbuka di atas kalau sempat, supaya Fase B-nya lebih tepat sasaran).

## FASE G — Revisi Besar Setelah Demo Internal ✅ SELESAI

1. **Master Barang dihapus total.** Model, controller, view, migration, route
   (`master.*`), dan link sidebar semuanya dibuang. Sekarang cuma ada 2 tabel
   utama di Logistik: Bahan Pokok dan Barang Inventaris - keduanya berdiri
   sendiri tanpa katalog referensi lagi. Input nama bahan/barang jadi teks bebas
   di form (tidak ada lagi datalist saran dari Master Barang).

2. **Menu Kategori:**
   - Kolom "Dipakai Oleh" sekarang cuma tampilkan "X Bahan Pokok, Y Barang
     Inventaris" (kata "transaksi" dihilangkan, hitungan Master Barang dibuang).
   - Kolom Aksi diganti jadi 1 tombol **Detail** yang buka popup ringkasan data,
     dan di BAWAH popup itu ada tombol **Edit** & **Hapus**. Pola ini diterapkan
     ke SEMUA tabel: Kategori, Supplier, Bahan Pokok, Barang Inventaris.
   - Khusus Barang Inventaris, karena punya data riwayat perawatan yang perlu
     ditampilkan juga, popup Detail-nya pakai pendekatan reload halaman
     (`?terpilih=ID`) supaya bisa render tabel riwayat lengkap dari server,
     bukan client-side murni seperti 3 tabel lainnya.

3. **Kolom tabel Bahan Pokok diringkas** jadi: No, Nama Bahan, Merk, Nama
   Supplier, Kontak Supplier, Qty, Harga per Item, Total Harga, Aksi (Detail).
   Kategori & tanggal transaksi tetap ada di form input dan di dalam popup
   Detail, cuma tidak lagi jadi kolom tabel utama. Tombol "Tambah Transaksi"
   diganti jadi **"Tambah Bahan"**.

4. **Kolom tabel Barang Inventaris diringkas** jadi: No, Nama Barang, Merk,
   Tanggal Beli, Qty, Harga per Item, Status Barang, Aksi (Detail). Tombol
   diganti jadi **"Tambah Inventaris"**.

5. **Input "Dapur Tujuan" DIHAPUS SEMENTARA** dari form Bahan Pokok maupun
   Barang Inventaris, begitu juga dropdown filter "Semua Dapur" di kedua
   halaman index-nya. Supaya form tetap bisa disimpan tanpa dapur, kolom
   `dapur_id` di migration `bahan_pokok` dan `barang_inventaris` diubah jadi
   **nullable** (langsung diedit di file migration aslinya, belum pernah
   dijalankan sungguhan jadi aman, tidak perlu migration alter terpisah).

   **DAMPAK PENTING yang perlu disadari:** karena `dapur_id` tidak lagi diisi,
   fitur "Admin Dapur lapor pemakaian bahan" (`dapur.beranda`,
   `laporan_pemakaian_bahan`) SEMENTARA TIDAK AKAN MENAMPILKAN DATA APA-APA,
   karena sistem tidak lagi tahu bahan mana untuk dapur mana. Ini bukan bug -
   ini konsekuensi langsung dari instruksi menghapus input dapur. Kalau nanti
   fitur pemilihan dapur mau dikembalikan, tinggal munculkan lagi field
   `dapur_id` di form (sudah ada strukturnya di database, tidak perlu migration
   baru, cukup ubah controller validasi + tambahkan kembali dropdown di view).

Semua file baru sudah dicek ulang programatis: keseimbangan tag `<div>`/`<form>`,
dan 0 tanda kutip dua literal di dalam atribut `x-data` manapun.

Seeder disesuaikan: Master Barang dibuang dari seeding, `dapur_id` tidak diisi
lagi di contoh transaksi (supaya konsisten dengan alur form yang sekarang tidak
mengumpulkan data itu).
## FASE I — Kembalikan Dapur Tujuan ✅ SELESAI

Sesuai permintaan, fitur "Dapur Tujuan" dikembalikan seperti sebelum Fase G:

- Migration `bahan_pokok` & `barang_inventaris`: `dapur_id` kembali **wajib**
  (bukan nullable lagi).
- `BahanPokokController` & `BarangInventarisController`: validasi `dapur_id`
  kembali `required`, filter dropdown "Semua Dapur" di halaman index aktif lagi.
- View `bahan_pokok.blade.php` & `barang_inventaris.blade.php`: form
  Tambah/Edit punya field "Dapur Tujuan"/"Dapur Pemilik" (wajib), filter dropdown
  di atas tabel dikembalikan, dan info Dapur ditambahkan ke popup Detail
  (TIDAK ditambahkan sebagai kolom baru di tabel utama - kolom tabel tetap
  sesuai daftar ringkas dari revisi sebelumnya, sesuai instruksi "itu saja yang
  harus diperbaiki").
- Seeder: `dapur_id` diisi lagi di data contoh transaksi.

**Konsekuensi baik:** karena `dapur_id` wajib lagi, fitur Admin Dapur (lapor
pemakaian bahan) yang sempat "kosong" di Fase G sekarang otomatis berfungsi
lagi tanpa perlu perubahan tambahan di sisi `DapurController`/`dapur_beranda`.

Semua file yang diedit sudah dicek ulang: keseimbangan tag `<div>`/`<form>`, dan
0 tanda kutip dua di dalam `x-data`.

## FASE J — Bahan Pokok: Alur "Tambah Transaksi" Jadi Card Batch ✅ SELESAI

Tombol "Tambah Bahan" (single-item modal) diganti jadi **"Tambah Transaksi"**
dengan alur baru, terinspirasi dari pola lama "Tambah Produksi":

1. Isi HEADER dulu: **Tanggal** + **Dapur Tujuan** (1 transaksi = 1 tanggal + 1
   dapur, berlaku untuk semua bahan di dalamnya).
2. Di bawahnya ada **Daftar Bahan** - tiap baris berisi field yang PERSIS sama
   dengan skema Fase I (Nama Bahan, Merk, Kategori, Supplier, Quantity, Satuan,
   Harga per Item).
3. Klik tombol **"+ Tambah Bahan"** (ikon plus, garis putus-putus) untuk
   menambah baris baru. Tiap baris (kecuali baris pertama) punya tombol silang
   di pojok kanan atas untuk menghapus baris itu.
4. Submit sekali ("Simpan Semua Bahan") langsung membuat banyak baris
   `bahan_pokok` sekaligus, semuanya berbagi tanggal & dapur yang sama.

**Backend:** method baru `BahanPokokController::simpanBatch()` - validasi
`bahan.*.field` (array nested), lalu loop create satu-satu. Route baru:
`POST /logistik/bahan-pokok/simpan-batch` (`bahanPokok.simpanBatch`).

**Quick-add Kategori/Supplier tetap bisa dipakai di dalam form batch ini** -
saya tambahkan penanda `modalAsal` ('single' vs 'batch') supaya begitu selesai
tambah kategori/supplier baru, sistem tahu harus kembali ke popup batch atau
popup edit single, bukan ketuker.

**Tabel tampilan (daftar transaksi) TIDAK diubah** - tetap kolom No, Nama Bahan,
Merk, Nama Supplier, Kontak Supplier, Qty, Harga/Item, Total Harga, Aksi, persis
seperti sebelumnya. Modal Edit (dari tombol Detail) juga tidak berubah - masih
edit satu baris seperti biasa, cuma sekarang dipisah jadi Alpine state sendiri
supaya tidak tercampur dengan alur batch yang baru.

File sudah dicek ulang: 60 `<div>`/60 `</div>`, 4 `<form>`/4 `</form>`, 5
`<template>`/5 `</template>`, dan 0 tanda kutip dua di dalam `x-data`.

**Catatan:** ini baru diterapkan untuk Bahan Pokok saja sesuai instruksi ("ini
aja dulu"). Barang Inventaris masih pakai alur modal single-item seperti
sebelumnya (Fase I), belum diubah ke pola card-batch.

## Fase K — Revisi Card, Master Barang, & Dapur Mandiri (permintaan lanjutan)
- Bahan Pokok: "Lihat Detail" di card sekarang membuka **halaman baru** (bukan dropdown),
  berisi detail semua bahan + total harga keseluruhan, bisa **di-print (nota)** atau
  **diunduh sebagai CSV/Excel**. Route: `bahanPokok.detailKelompok`, `bahanPokok.exportKelompok`.
- Card index: font tanggal diperbesar, nama dapur disamakan ukurannya (tidak terlalu besar).
- Halaman Tambah Transaksi: Kategori, Satuan, dan Catatan dihapus dari tampilan (Kategori &
  Satuan otomatis ikut Master Barang yang dipilih); ditambah kolom **Subtotal** per baris.
- Master Barang: form input sekarang diawali pilihan **Jenis** (Bahan Pokok / Inventaris).
  Bahan Pokok punya Satuan (opsional), Inventaris tidak. Migration `add_jenis_to_master_barang`.
- Dapur: tombol **Tambah Dapur** (admin logistik) sekaligus membuat akun login Admin Dapur
  dalam satu form (`DapurController::simpan`).
- Admin Dapur bisa **Catat Pembelian Mandiri** (`dapur.formBeliMandiri` /
  `dapur.simpanBeliMandiri`) saat bahan kiriman ternyata kurang — tersimpan di tabel
  `bahan_pokok` yang sama dengan flag `sumber = dapur`, dan tetap terlihat oleh admin
  logistik di halaman Detail Dapur / Laporan Dapur (badge "Mandiri oleh Dapur").
  Migration `add_sumber_to_bahan_pokok`.

## Fase L — Quick-Add, Overview Dapur, Detail Dapur, & Wording Beli Mandiri ✅ SELESAI

1. **Merk dihapus dari Master Barang.** Kolom `merk` dibuang dari migration
   `create_master_barang_table` (belum pernah dijalankan sungguhan, jadi aman
   diedit langsung), `fillable` model, validasi controller, dan tampilan
   `master_barang_indeks.blade.php` (kolom tabel, form Tambah/Edit, popup
   Detail). Alasannya: merk barang yang dibeli beda-beda tiap hari, jadi
   sekarang HANYA diisi manual per transaksi (Bahan Pokok / Barang Inventaris),
   bukan bagian dari katalog master.

2. **Quick-add "+" untuk Nama Bahan & Supplier** di halaman Tambah Transaksi:
   - `bahan_pokok_tambah.blade.php` (Logistik) dan `dapur_beli_mandiri.blade.php`
     (Beli Mandiri Dapur) sekarang punya tombol "+" kecil di sebelah dropdown
     Nama Bahan maupun Supplier di tiap baris.
   - Klik tombol itu membuka popup form (sama persis fieldnya dengan Master
     Barang / Supplier) TANPA reload halaman dan TANPA kehilangan isian form
     yang sedang diketik.
   - Setelah simpan, hasilnya langsung masuk ke daftar pilihan (via AJAX/JSON)
     dan langsung terpilih di baris yang sedang diisi.
   - `MasterBarangController::simpan()` sekarang juga membalas JSON kalau
     dipanggil via AJAX (pola yang sama dengan `KategoriController` &
     `SupplierController` yang sudah lebih dulu mendukung ini).
   - **Route `supplier.simpan` & `masterBarang.simpan` dipindah keluar dari
     grup middleware `role:admin_logistik`** ke grup `auth` umum, supaya Admin
     Dapur juga bisa memakainya dari halaman Beli Mandiri (menu Master Barang /
     Supplier itu sendiri tetap khusus Admin Logistik - cuma endpoint
     `simpan`-nya yang dipakai bareng).

3. **Overview Dapur: hitungan "Transaksi Bahan" diperbaiki.** Sebelumnya
   menghitung jumlah BARIS `bahan_pokok` (kalau 1 kali "Tambah Transaksi" isi
   10 macam bahan, salah terbaca sebagai 10 transaksi). Sekarang menghitung
   jumlah TRANSAKSI sungguhan lewat method baru `Dapur::jumlahTransaksiBahanPokok()`
   (distinct tanggal_transaksi per dapur).

4. **Detail Dapur dirombak:**
   - Tabel "Transaksi Bahan Pokok yang Dikirim ke Dapur Ini" sekarang
     dikelompokkan PER TRANSAKSI (per tanggal), bukan lagi per baris bahan.
     Tiap baris tabel menampilkan jumlah bahan, total harga, badge "ada Mandiri
     oleh Dapur" (kalau ada baris dengan sumber = dapur), dan status laporan
     dapur ringkas (Belum Ada / X dari Y Dilaporkan / Lengkap Dilaporkan).
   - Tombol **Detail** di tiap baris membuka halaman yang SAMA dengan "Detail
     Kelompok" di menu Bahan Pokok Logistik (`bahanPokok.detailKelompok`) -
     halaman itu sekarang punya kolom tambahan **Status Laporan Dapur** per
     bahan (badge habis/sisa/kurang/belum lapor), dan CSV export-nya juga ikut
     menyertakan kolom itu.
   - Ditambahkan 2 kartu ringkasan baru di atas: **Total Transaksi Bahan
     Pokok** dan **Total Transaksi Barang Inventaris**, berdampingan dengan
     kartu Total Biaya Perawatan Inventaris yang sudah ada sebelumnya. Method
     baru di model `Dapur`: `totalTransaksiBahanPokok()` &
     `totalTransaksiBarangInventaris()`.

5. **Wording "Beli Mandiri" diperluas.** Sebelumnya teks di `dapur_beranda`
   dan `dapur_beli_mandiri` menyiratkan fitur ini HANYA untuk kasus bahan
   kiriman kurang. Sekarang kalimatnya diubah supaya jelas fitur ini juga bisa
   dipakai untuk kebutuhan bahan mendadak lain di dapur (bukan cuma kekurangan
   kiriman) - karena admin logistik tidak selalu bisa memprediksi kebutuhan
   dadakan tiap dapur.

Semua file yang diedit/dibuat sudah dicek ulang: keseimbangan tag
`<div>`/`</div>`, `<form>`/`</form>`, `<template>`/`</template>`, `<table>`/`</table>`,
dan 0 tanda kutip dua literal di dalam atribut `x-data` manapun (pelajaran dari
bug "popup tidak muncul" sebelumnya).

**Catatan:** karena Master Barang sekarang tidak punya field Merk sama sekali,
kalau ada data lama (dari sesi sebelum fase ini) yang sempat mengisi kolom itu,
tidak masalah - kolomnya memang sudah dihapus total dari skema, bukan sekadar
disembunyikan.

## Fase M — Master Barang jadi Foreign Key Sungguhan (Bahan Pokok & Inventaris) ✅ SELESAI

**Latar belakang:** sebelumnya Master Barang cuma dipakai sebagai "contekan" JS saat
mengisi form (nama/kategori/satuan disalin jadi teks bebas ke bahan_pokok/barang_inventaris,
tanpa foreign key sungguhan). Fase ini mengubahnya jadi relasi FK betulan.

1. **Kategori: "Dipakai Oleh" diperbaiki.** Sebelumnya menghitung jumlah TRANSAKSI yang
   memakai kategori itu (`bahan_pokok_count`, `barang_inventaris_count`). Sekarang
   menampilkan JENIS yang memakainya (badge "Bahan Pokok" / "Inventaris"), dicek lewat
   `KategoriBarang::dipakaiUntukBahanPokok()` / `dipakaiUntukInventaris()` (query ke Master
   Barang dengan filter kolom `jenis`) - karena Bahan Pokok & Barang Inventaris sendiri
   sudah tidak punya kategori_id lagi (lihat poin 2).

2. **Master Barang jadi Foreign Key ke Bahan Pokok & Barang Inventaris.**
   - Migration `bahan_pokok` & `barang_inventaris`: kolom `nama_bahan`/`nama_barang`,
     `kategori_id`, `satuan` (di bahan_pokok) DIHAPUS TOTAL - diganti 1 kolom
     `master_barang_id` (foreign key beneran, dengan `restrictOnDelete()` sebagai
     pengaman tambahan di level database).
   - Migration `master_barang` DIPINDAH urutannya (dari `2026_09_07_...` ke
     `2026_08_15_000003_...`) supaya jalan SEBELUM `bahan_pokok`/`barang_inventaris`
     (aman diedit karena belum pernah dijalankan sungguhan).
   - Konsekuensinya: form Tambah Transaksi Bahan Pokok & Barang Inventaris **TIDAK LAGI
     ada input Kategori sama sekali** - otomatis "ikut" Master Barang lewat relasi.
   - Model `BahanPokok` & `BarangInventaris` dapat relasi `masterBarang()` + method
     bantu `namaBahan()`/`namaBarang()`/`namaKategori()`/`satuanBahan()`.

3. **Barang Inventaris: cara input diubah total meniru Bahan Pokok.** Dulu single-item
   lewat modal popup (`simpan()` satu baris). Sekarang ada halaman tersendiri
   **"Tambah Transaksi"** (`inventaris_tambah.blade.php`) yang bisa input banyak barang
   sekaligus dalam 1 transaksi (`simpanBatch()`), field disesuaikan (tanpa Supplier &
   Satuan yang memang tidak dipakai inventaris, tapi ada Status Barang per baris). Modal
   lama tetap ada tapi sekarang KHUSUS untuk mengedit 1 baris yang sudah ada.

4. **Histori transaksi tetap aman walau Master Barang diedit/dihapus - pakai SOFT
   DELETE, bukan mengganti pola FK.** `MasterBarang` sekarang pakai trait `SoftDeletes`
   (kolom `deleted_at`). Konsekuensinya:
   - Hapus Master Barang dari menunya = cuma ditandai `deleted_at`, BUKAN dibuang dari
     database. Otomatis hilang dari dropdown Tambah Transaksi yang baru (bawaan trait),
     tapi baris fisiknya tetap ada.
   - Relasi `masterBarang()` di `BahanPokok`/`BarangInventaris` pakai `->withTrashed()`,
     jadi histori transaksi lama TETAP bisa menampilkan nama/kategori/satuan aslinya
     walau Master Barang-nya sudah "dihapus" - method `namaBahan()`/`namaBarang()`
     otomatis menambahkan keterangan "(sudah dihapus dari katalog)" di kasus ini.
   - Edit Master Barang (bukan hapus) akan langsung terlihat di histori lama juga
     (karena betul-betul 1 baris yang sama diakses lewat relasi, bukan lagi salinan
     teks bebas) - ini konsekuensi wajar dari normalisasi FK, sudah didiskusikan.
   - `restrictOnDelete()` di migration jadi pengaman tambahan di level database kalau
     suatu saat ada yang forceDelete/hard-delete Master Barang secara langsung (bukan
     lewat aplikasi) - transaksi lama tidak akan ikut rusak/hilang.

5. **`DatabaseSeeder` ditulis ulang** mengikuti urutan baru: bikin Master Barang dulu,
   baru transaksi Bahan Pokok/Barang Inventaris mereferensikan `master_barang_id`-nya.

6. **Dibersihkan sekalian:** method `BahanPokokController::simpan()` (single-item, tidak
   pernah dipakai di view manapun) dan route `bahanPokok.simpan` dihapus karena mubazir
   (satu-satunya jalur simpan yang dipakai adalah `simpanBatch`).

Semua file yang diedit/dibuat sudah dicek ulang: keseimbangan tag, dan (PENTING, bug
nyata yang ditemukan & diperbaiki di fase ini) tidak ada tanda kutip dua literal di
dalam komentar JS manapun yang ada di dalam atribut `x-data="..."` - karena itu akan
memutus atribut HTML lebih awal di browser walau terlihat valid di kode PHP/Blade.

**Yang WAJIB dilakukan sebelum dipakai:** karena struktur tabel `bahan_pokok` dan
`barang_inventaris` berubah total (kolom dihapus, foreign key baru), kalau sebelumnya
sudah pernah `migrate` beneran dengan data asli, WAJIB `php artisan migrate:fresh --seed`
ulang (data lama TIDAK kompatibel dengan skema baru ini, tidak ada migration path
otomatis untuk konversi data lama karena scope-nya masih tahap pengembangan/TA).
