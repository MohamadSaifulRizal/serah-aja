# Buku Kas Dapur MBG

Aplikasi pencatatan logistik & kalkulator laba/rugi untuk dapur program **Makan Bergizi Gratis (MBG)** — dibangun dengan Laravel 12 + Tailwind CSS + Alpine.js.

Dirancang untuk **admin logistik tunggal** (1 akun, tanpa alur approval berjenjang): catat stok bahan, catat produksi tiap dapur, sistem otomatis hitung laba/rugi, dan rekap laba per periode.

---

## 1. Kebutuhan Sistem

| Kebutuhan | Versi Minimal |
|---|---|
| PHP | 8.2+ |
| Composer | 2.x |
| Ekstensi PHP | `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` |

Database default: **SQLite** (tidak perlu setup server database terpisah).

---

## 2. Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Buka browser ke `http://127.0.0.1:8000`.

> **Wajib `migrate:fresh`**, bukan `migrate` biasa — struktur database sudah dirombak total di versi ini.

---

## 3. Login

Login pakai **Username** (bukan email).

| Username | Password |
|---|---|
| `admin` | `rahasia123` |

Aplikasi ini cuma punya **1 tipe akun** (tidak ada perbedaan role) — siapa pun yang login otomatis punya akses penuh ke semua fitur.

---

## 4. Struktur Menu

- **Dashboard** — ringkasan laba bulan ini, grafik tren laba 6 bulan terakhir, status stok bahan olahan.
- **Logistik** ▾
  - **Master Barang** — daftar semua bahan (Olahan & Inventaris), kategori bebas isi sendiri.
  - **Bahan Olahan (Stok)** — kartu stok masuk/keluar per bahan habis pakai.
  - **Bahan Inventaris** — riwayat perbaikan/penarikan/pengisian aset dapur.
- **Dapur** — daftar dapur (card), tiap dapur punya riwayat produksi & laba.
- **Laba** — rekap laba harian/mingguan/bulanan + akumulasi total sepanjang waktu.

---

## 5. Alur Kerja

### A. Daftarkan Bahan
1. **Logistik → Master Barang → + Daftarkan Barang**.
2. Pilih jenis: **Bahan Olahan** (habis pakai, perlu satuan) atau **Bahan Inventaris** (aset tetap).
3. Kategori diisi bebas (ketik langsung, tidak perlu didaftarkan dulu — sistem akan menyarankan kategori yang pernah dipakai).

### B. Catat Stok Masuk (Beli Bahan)
1. **Logistik → Bahan Olahan (Stok)**, pilih bahan di kiri.
2. Klik **+ Stok Masuk (Beli)**, isi tanggal, jumlah, harga beli, dan sumber beli (opsional, teks bebas — mis. nama pasar/toko).

### C. Tambah Dapur & Catat Produksi (Inti Kalkulator Laba)
1. **Dapur → + Tambah Dapur**, isi nama, alamat, penanggung jawab.
2. Buka dapur tersebut, klik **Catat Produksi Baru**.
3. Centang bahan yang dipakai, isi jumlah & harga satuan per bahan.
4. Isi **jumlah porsi dihasilkan** dan **harga jual per porsi**.
5. Kalkulator di bawah form otomatis menghitung **Total Modal**, **Total Pendapatan**, dan **Estimasi Laba** secara langsung saat Anda mengisi.
6. Simpan — stok bahan olahan otomatis berkurang (tercatat sebagai stok keluar), dan laba tersimpan.

**Contoh perhitungan:**
> 1 kg ayam (Rp20.000) dipakai untuk 5 porsi, dijual Rp15.000/porsi
> → Pendapatan = 5 × Rp15.000 = Rp75.000
> → Modal = Rp20.000
> → **Laba = Rp55.000**

### D. Lihat Rekap Laba
1. Buka menu **Laba**.
2. Pilih periode: **Harian**, **Mingguan**, atau **Bulanan**, dan tanggal acuan.
3. Lihat rincian laba per dapur untuk periode tersebut, plus pengeluaran operasional (listrik, gas, gaji kru, dll — bisa ditambah langsung dari halaman ini lewat tombol **+ Tambah Pengeluaran**).
4. Bagian bawah halaman menampilkan **akumulasi total laba bersih sepanjang waktu** (tidak terikat filter periode).

### E. Riwayat Aset (Bahan Inventaris)
1. **Logistik → Bahan Inventaris**, pilih aset di kiri.
2. Klik **+ Catat Riwayat Baru**, pilih jenis aksi: **Perbaikan**, **Penarikan**, atau **Pengisian**, isi biaya (opsional) dan catatan.

---

## 6. Troubleshooting

**"could not find driver" saat migrate**
→ Aktifkan ekstensi PHP `pdo_sqlite` (`php -m | grep sqlite`).

**Halaman blank / error 500**
→ Cek `storage/logs/laravel.log`. Biasanya `APP_KEY` belum di-generate atau folder `storage`/`bootstrap/cache` tidak writable (`chmod -R 775 storage bootstrap/cache`).

**Login gagal padahal username/password benar**
→ Pastikan sudah `php artisan migrate:fresh --seed` (bukan `migrate` saja).

---

## 7. Reset Aplikasi

```bash
php artisan migrate:fresh --seed
```

## 8. Dokumentasi Tambahan

- `PROGRESS.md` — checklist lengkap seluruh riwayat pengembangan, termasuk perombakan besar dari versi multi-role ke versi admin tunggal (Fase 11).
- `README_PERUBAHAN.md` — catatan teknis detail struktur data versi-versi sebelumnya.
