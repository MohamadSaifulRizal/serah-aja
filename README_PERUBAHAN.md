# Dokumentasi Perubahan — Buku Kas Dapur MBG

Sudah 8 fase perbaikan selesai dikerjakan. Lihat `PROGRESS.md` untuk checklist detail per fase.
Fase 8 adalah penyelarasan fitur dengan project statis HTML/JS referensi yang Anda kirimkan, sekaligus penyederhanaan besar-besaran supaya tidak terlalu rumit.

## Ringkasan Alur Terbaru

1. **Master** login → bisa akses SEMUA halaman tanpa kecuali (untuk kontrol penuh & testing), dan bisa membuat akun **Admin, Akuntan, Ahli Gizi**.
2. **Admin** login → bisa membuat akun **Akuntan, Ahli Gizi, Supplier**, dan akses semua menu operasional.
3. **Akuntan** → login ke dashboard sendiri, meninjau & menyetujui/menolak Menu, membuat Transaksi Pembelian ke Supplier.
4. **Ahli Gizi** → login ke dashboard sendiri, mengajukan Menu baru (pilih bahan lewat tabel checkbox + isi jumlah per porsi).
5. **Supplier** → login ke dashboard sendiri, hanya melihat daftar pesanan (PO) yang ditujukan ke dirinya. **Supplier sekarang adalah akun User biasa** (bukan entitas terpisah) — dibuat oleh Admin cukup dengan Nama, Username, Password, persis seperti akun role lain.
6. Alur pembelian: Ahli Gizi ajukan Menu → Akuntan setujui (sistem otomatis hitung kebutuhan bahan = resep × porsi − stok tersedia) → Akuntan/Admin buat Transaksi Pembelian ke Supplier (jumlah otomatis terisi dari kekurangan) → saat ditandai "Diterima", stok bahan olahan otomatis bertambah dan tercatat di Kartu Stok lengkap dengan nama Supplier asalnya.
7. Kategori Barang tetap ada sebagai master data, tapi dikelola lewat modal di halaman Master Barang (tidak dipisah jadi halaman sendiri) supaya sidebar tidak terlalu panjang.
8. Sidebar memakai model **dropdown/collapsible** (Inventaris Barang, Keuangan Dapur, Menu & Pembelian) sesuai preferensi Anda — bukan daftar menu datar.

## Cara Menjalankan Migrasi

```bash
composer install
cp .env.example .env   # jika belum ada .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

> **Wajib pakai `migrate:fresh`** (bukan `migrate` biasa) karena struktur tabel Supplier berubah total di Fase 8 (tabel `suppliers` dihapus). Kalau sebelumnya sudah pernah migrate versi lama, jalankan `migrate:fresh --seed` untuk reset bersih.

## Akun Contoh (dari Seeder)

Password semua akun contoh: **rahasia123**

| Role | Username |
|---|---|
| Master | `master` |
| Admin | `admin` |
| Akuntan | `akuntan` |
| Ahli Gizi | `ahligizi` |
| Supplier | `supplier` (CV Sembako Jaya) |
| Supplier | `supplier2` (Pasar Tanjung Segar) |

Email tidak perlu diketahui/diingat — login pakai **username**, bukan email.

## Struktur Data Terkini

- `users` — semua akun (master/admin/akuntan/ahli_gizi/supplier) dalam satu tabel, dibedakan lewat kolom `role`. Supplier TIDAK punya tabel profil terpisah lagi.
- `kategori_barang` — master kategori barang (per jenis: olahan/inventaris)
- `barang` — master barang, kolom `kategori_id` (relasi), `stok_minimum` (opsional, untuk badge status "Menipis")
- `mutasi_stok` — kartu stok barang olahan, kolom `supplier_id` & `transaksi_supplier_id` (nullable) untuk jejak asal barang
- `menus` / `menu_item` — pengajuan menu + resep bahan per porsi
- `transaksi_supplier` / `transaksi_supplier_item` — PO pembelian ke supplier (`supplier_id` merujuk langsung ke `users.id`)

## Yang Perlu Diperhatikan

- Karena lingkungan pengerjaan saya tidak memiliki PHP/Composer untuk menjalankan migrasi atau tes otomatis, mohon jalankan `php artisan migrate:fresh --seed` di lokal/server Anda dan kabari saya kalau ada error spesifik (sertakan pesan errornya) — saya perbaiki cepat.
- Supplier saat ini hanya bisa **melihat** daftar PO. Kalau nanti supplier perlu update status pengiriman sendiri, tinggal ditambahkan.
- Kalau nanti Anda ingin tampilan yang lebih mirip 1:1 dengan project statis (misalnya kartu ringkasan bergaya "stamp" atau warna badge tertentu), tinggal kabari — struktur datanya sudah selaras, tinggal polish visual.

## Fitur Baru (Fase 10)

- **Kuitansi Cetak**: buka Detail Transaksi Supplier → tombol "Cetak Kuitansi" → halaman baru terbuka, siap print/simpan PDF.
- **Histori Menu Harian**: menu sidebar baru "Histori Menu Harian" (grup Menu & Pembelian, khusus Master/Admin/Akuntan) — rekap semua menu per tanggal.
- **Laporan Bulanan → Cetak/Unduh PDF**: tombol di halaman Laporan Bulanan, membuka halaman print-friendly dengan rincian lengkap. Untuk "download" PDF sungguhan, gunakan fitur print bawaan browser (Ctrl+P / Cmd+P → pilih "Save as PDF").
- **Dashboard dengan Grafik**: Dashboard Master/Admin sekarang menampilkan 2 chart (tren 6 bulan & komposisi kas bulan ini) pakai Chart.js via CDN — tidak perlu instalasi tambahan.
- **Tabel Responsive**: semua tabel sudah bisa digeser ke samping di HP (sebelumnya ada beberapa yang kepotong karena salah pakai `overflow-hidden`).
- **Badge & tombol tanpa emoji**: diganti dengan komponen SVG icon + font-bold yang konsisten.

### Opsional: PDF generate otomatis di server (bukan lewat print browser)

Kalau ke depannya Anda mau tombol "Unduh PDF" benar-benar generate file PDF di server (bukan lewat dialog print), install package ini:

```bash
composer require barryvdh/laravel-dompdf
```

Lalu beri tahu saya — saya akan sambungkan ke view cetak yang sudah ada (`laporan_bulanan_cetak.blade.php` dan `transaksi_cetak.blade.php` sudah didesain agar mudah dikonversi).

---

## Perubahan: Manajemen Akun & penghapusan Pengeluaran Operasional

- **Dihapus**: menu/route/controller/model/view Pengeluaran Operasional, kartu di Dashboard, dan tabel `pengeluaran_operasional` (migrasi `2026_09_01_000001`).
- **Role baru (3 role)**: `super_admin`, `admin_logistik`, `admin_dapur` (migrasi `2026_09_01_000002` mengubah kolom `role` dari enum menjadi string).
- **Menu Manajemen Akun** (`/manajemen-akun`): hanya Super Admin. Bisa tambah, edit (nama, username, role, dapur, reset password), dan hapus akun Admin Logistik / Admin Dapur. Super Admin tidak bisa menghapus/mengubah akun Super Admin lewat menu ini.
- Super Admin mendapat SEMUA menu Admin Logistik, ditambah **Manajemen Akun** tepat di bawah menu Dapur. Admin Logistik & Admin Dapur tidak melihat menu ini dan akan dialihkan jika membuka URL-nya. Tambah Dapur di menu Dapur kini hanya membuat data dapur; akun Admin Dapur dibuatkan Super Admin lalu dikaitkan ke dapur.
- Akun contoh seeder: `superadmin` / `rahasia123`.
- Untuk database yang sudah ada: `php artisan migrate`, lalu buat Super Admin pertama, mis. `php artisan tinker` →
  `User::create(['name'=>'Super Admin','username'=>'superadmin','email'=>'superadmin@mbg.local','role'=>'super_admin','password'=>'rahasia123']);`

---

## Perubahan Oktober 2026: Kategori Dipisah, Menu Logistik Dihapus, Kondisi Barang & Export Excel

- **Sidebar**: dropdown "Logistik" dihapus. Sekarang menu utama: Dashboard, **Kategori Bahan Pokok**, **Kategori Barang Inventaris**, Supplier, Master Barang, **Bahan Pokok**, **Barang Inventaris**, Dapur (+ Manajemen Akun untuk Super Admin).
- **Kategori dipisah per jenis** lewat kolom baru `kategori_barang.jenis` (`bahan_pokok` / `inventaris`). Nama kategori unik per jenis. Pilihan kategori di Master Barang & popup "+" otomatis hanya menampilkan kategori sesuai jenisnya. URL: `/kategori-bahan-pokok` dan `/kategori-barang-inventaris`.
- **Barang Inventaris** punya field baru `kondisi_barang` (`baru`, `baik`, `rusak_ringan`, `rusak_berat`) - ada di form Tambah (per baris), Edit, Detail, tabel, dan tabel inventaris di halaman Dapur. Tabel Barang Inventaris juga menampilkan kolom **Dapur Pemilik**.
- **Export Excel (.xlsx)** - tanpa library tambahan (class `App\Support\XlsxWriter`, hanya butuh ekstensi PHP `zip`):
  - Barang Inventaris: tombol *Export to Excel* mengikuti filter dapur (Semua Dapur / dapur tertentu). Export semua dapur punya sheet tambahan "Rekap Kondisi per Dapur".
  - Bahan Pokok > Detail Transaksi: tombol Unduh CSV diganti *Export to Excel* (.xlsx).
  - Bahan Pokok: tombol *Export Semua Transaksi (Excel)* - sheet "Semua Transaksi" + "Rekap per Transaksi".
- **Migrasi**: jalankan `php artisan migrate` (tidak perlu `migrate:fresh`). Kategori lama yang dipakai bersama otomatis dipecah: kategori yang dipakai Master Barang inventaris dipindah/disalin ke jenis inventaris. Barang inventaris lama diberi kondisi `baik`.
- **Dapur**: Alamat & Penanggung Jawab kini WAJIB diisi (tambah & edit). Tiap card dapur punya tombol **Edit** dan **Hapus**; dapur yang sudah punya transaksi Bahan Pokok/Barang Inventaris atau masih terkait akun Admin Dapur tidak bisa dihapus (pesan error ditampilkan).

---

## Update Kondisi Barang Inventaris (Logistik & Admin Dapur) + Tailwind v4

- **Update kondisi** (`baru`/`baik`/`rusak_ringan`/`rusak_berat`) lewat tombol **Update Kondisi** di tabel & detail Barang Inventaris. Setiap update menyimpan **tanggal update**, **siapa yang mengupdate**, kondisi sebelum -> sesudah, dan catatan (tabel baru `riwayat_kondisi_inventaris`; ringkasan terakhir di kolom `kondisi_diperbarui_pada` & `kondisi_diperbarui_oleh`). Kondisi awal saat barang diinput dan perubahan kondisi lewat form Edit juga tercatat.
- **Admin Dapur**: di halaman *Dapur Saya* ada tabel Barang Inventaris dengan tombol Update Kondisi + riwayatnya, dan tombol **Export to Excel** - HANYA barang milik dapurnya sendiri (dicek di server; barang dapur lain -> 403). Admin Dapur hanya bisa mengubah kondisi, bukan harga/qty/dapur.
- **Export Excel Inventaris** kini punya kolom *Tanggal Update Kondisi* & *Diupdate Oleh* + sheet **Riwayat Kondisi** (logika di `App\Support\LaporanInventaris`, dipakai sisi Logistik & Dapur).
- **Tailwind**: layout sebelumnya memuat **Tailwind v3** (`cdn.tailwindcss.com`) padahal editor memakai **v4**, sehingga saran editor (mis. `min-w-160`) tidak akan jalan di browser. Sekarang layout memakai **Tailwind v4 browser build** (`resources/views/komponen/tailwind.blade.php`; warna/font di blok `@theme`, plus penyesuaian agar border & cursor tombol tetap seperti v3). Kelas arbitrary diganti ke skala v4 (`min-w-[640px]` -> `min-w-160`, `z-[60]` -> `z-60`, `w-4 h-4` -> `size-4`, `flex-shrink-0` -> `shrink-0`, `bg-gradient-to-r` -> `bg-linear-to-r`, `outline-none` -> `outline-hidden`, dst).
- **Migrasi**: `php artisan migrate` (tabel baru + 2 kolom; data lama aman).

---

## Biaya Update Kondisi & Portal Admin Dapur (Dashboard + menu seperti Admin Logistik)

- **Biaya pada Update Kondisi**: popup Update Kondisi punya field **Biaya (Rp)** manual & WAJIB diisi (isi 0 jika tidak ada biaya) (label menyesuaikan: *Biaya Perbaikan* untuk perbaikan, *Biaya Membeli Barang Pengganti* untuk rusak berat -> baru). Biaya tampil di **Riwayat Perubahan Kondisi** (popup), dan tersimpan di kolom `riwayat_kondisi_inventaris.biaya`.
- **Management Dapur**: detail dapur menampilkan **Total Biaya Update Kondisi Inventaris**, kolom *Biaya Update Kondisi* per barang, dan tabel **Riwayat Update Kondisi & Biayanya**; card dapur di Overview juga menampilkan total biayanya. Export Excel inventaris memuat kolom & total biaya (sheet Barang Inventaris + Riwayat Kondisi).
- **Portal Admin Dapur** - menu: **Dashboard, Kategori Bahan Pokok, Kategori Barang Inventaris, Bahan Pokok, Barang Inventaris**:
  - *Dashboard*: isi yang sama dengan detail dapur di Management Dapur, dalam bentuk diagram (komponen `komponen/diagram-batang`) dan hanya untuk dapurnya sendiri.
  - *Kategori*: sama seperti Admin Logistik (kategori bersifat global).
  - *Bahan Pokok*: daftar transaksi dapurnya, detail, export Excel, **Pembelian Mandiri** (menggantikan Tambah Transaksi), dan **Lapor** status bahan di halaman detail. Edit/Hapus hanya untuk bahan hasil Pembelian Mandiri dapur itu sendiri (bahan kiriman logistik tidak bisa diubah dapur).
  - *Barang Inventaris*: fitur sama seperti Admin Logistik (tambah, edit, hapus, update kondisi + biaya, riwayat perawatan, export Excel) tetapi otomatis hanya untuk dapurnya sendiri; `dapur_id` dipaksa dari akun di sisi server.
  - Route halaman Bahan Pokok & Barang Inventaris dipakai bersama (`/logistik/bahan-pokok`, `/logistik/barang-inventaris`); pembatasan dapur dilakukan di controller (`Controller::dapurTerbatas()`).
- **Migrasi**: `php artisan migrate` (menambah kolom `biaya`).
