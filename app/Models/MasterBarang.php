<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBarang extends Model
{
    // PENTING: SoftDeletes dipakai supaya kalau Master Barang "dihapus" dari
    // menunya, baris ini cuma ditandai deleted_at (bukan betul-betul dibuang).
    // Riwayat transaksi Bahan Pokok / Barang Inventaris yang sudah pernah
    // mereferensikan master_barang_id ini TETAP UTUH & tetap bisa menampilkan
    // nama/kategori/satuan aslinya, walau barangnya sudah tidak ada lagi di
    // katalog aktif (otomatis hilang dari dropdown Tambah Transaksi karena
    // query biasa - MasterBarang::all(), ::where(), dst - otomatis mengecualikan
    // baris yang soft-deleted, itu bawaan trait ini).
    use SoftDeletes;

    protected $table = 'master_barang';
    protected $fillable = ['nama_barang', 'jenis', 'kategori_id', 'satuan'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    // Dipakai di Master Kategori untuk menampilkan "dipakai untuk apa" tanpa
    // withTrashed() - kalau semua Master Barang jenis tertentu di kategori ini
    // sudah dihapus semua, kategori itu dianggap sudah tidak dipakai jenis itu.
    public function bahanPokok(): HasMany
    {
        return $this->hasMany(BahanPokok::class);
    }

    public function barangInventaris(): HasMany
    {
        return $this->hasMany(BarangInventaris::class);
    }

    public function labelJenis(): string
    {
        return $this->jenis === 'inventaris' ? 'Inventaris' : 'Bahan Pokok';
    }
}
