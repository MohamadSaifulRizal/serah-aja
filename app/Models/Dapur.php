<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Dapur extends Model
{
    protected $table = 'dapur';
    protected $fillable = ['nama_dapur', 'alamat', 'penanggung_jawab', 'catatan'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Semua transaksi bahan pokok yang pernah dikirim ke dapur ini.
    public function bahanPokok(): HasMany
    {
        return $this->hasMany(BahanPokok::class)->orderBy('tanggal_transaksi', 'desc');
    }

    // Semua transaksi barang inventaris milik dapur ini.
    public function barangInventaris(): HasMany
    {
        return $this->hasMany(BarangInventaris::class)->orderBy('tanggal_beli', 'desc');
    }

    // Semua riwayat update kondisi dari barang inventaris milik dapur ini.
    public function riwayatKondisiInventaris(): HasManyThrough
    {
        return $this->hasManyThrough(RiwayatKondisiInventaris::class, BarangInventaris::class, 'dapur_id', 'barang_inventaris_id');
    }

    // Total biaya yang dikeluarkan lewat fitur Update Kondisi (perbaikan / beli pengganti)
    // untuk seluruh barang inventaris dapur ini.
    public function totalBiayaUpdateKondisiInventaris(): float
    {
        return (float) $this->riwayatKondisiInventaris()->sum('riwayat_kondisi_inventaris.biaya');
    }

    public function totalBiayaPerawatanInventaris(): float
    {
        return (float) $this->barangInventaris->sum(fn (BarangInventaris $b) => $b->totalBiayaPerawatan());
    }

    // Total nominal SELURUH transaksi bahan pokok yang pernah dikirim/dibeli untuk dapur ini.
    public function totalTransaksiBahanPokok(): float
    {
        return (float) $this->bahanPokok->sum('total_harga');
    }

    // Total nominal SELURUH transaksi barang inventaris milik dapur ini.
    public function totalTransaksiBarangInventaris(): float
    {
        return (float) $this->barangInventaris->sum('total_harga');
    }

    // PENTING: yang dihitung di sini adalah JUMLAH TRANSAKSI (1 transaksi = 1
    // tanggal, bisa berisi banyak baris bahan sekaligus lewat "Tambah Transaksi"),
    // BUKAN jumlah baris bahan_pokok. Kalau dihitung per baris, 1 kali belanja
    // 10 macam bahan akan salah terbaca sebagai "10 transaksi".
    public function jumlahTransaksiBahanPokok(): int
    {
        return $this->bahanPokok()->select('tanggal_transaksi')->distinct()->count('tanggal_transaksi');
    }

    // Transaksi bahan pokok yang MASUK ke dapur ini tapi belum dilaporkan admin dapur
    // (belum ada laporan habis/sisa/kurang) - dipakai untuk "PR" di dashboard admin dapur.
    // Bahan yang dibeli MANDIRI oleh dapur sendiri (sumber = dapur) tidak perlu dilaporkan
    // ke diri sendiri, jadi dikecualikan dari daftar ini.
    public function bahanPokokBelumDilaporkan(): HasMany
    {
        return $this->bahanPokok()->doesntHave('laporan')->where('sumber', 'logistik');
    }
}
