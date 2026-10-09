<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BahanPokok extends Model
{
    use HasFactory;

    protected $table = 'bahan_pokok';
    protected $fillable = [
        'master_barang_id', 'merk', 'supplier_id', 'dapur_id', 'sumber',
        'tanggal_transaksi', 'quantity', 'harga_per_item', 'total_harga', 'catatan',
    ];

    protected function casts(): array
    {
        return ['tanggal_transaksi' => 'date'];
    }

    protected static function booted(): void
    {
        // total_harga selalu dihitung otomatis, tidak pernah diinput manual,
        // supaya tidak ada risiko datanya tidak sinkron.
        static::saving(function (BahanPokok $b) {
            $b->total_harga = round($b->quantity * $b->harga_per_item, 2);
        });
    }

    // PENTING: withTrashed() di sini supaya kalau Master Barang yang dipakai
    // transaksi ini sudah "dihapus" (soft delete) dari katalog, relasi ini
    // TETAP bisa mengambil datanya (nama, kategori, satuan) untuk ditampilkan
    // di histori - bukan cuma balik null. Lihat method labelMasterBarang().
    public function masterBarang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class)->withTrashed();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    // 1 transaksi bahan_pokok idealnya cuma dilaporkan sekali oleh admin dapur (habis/sisa/kurang).
    public function laporan(): HasOne
    {
        return $this->hasOne(LaporanPemakaianBahan::class);
    }

    public function sudahDilaporkan(): bool
    {
        return $this->laporan()->exists();
    }

    // Nama bahan sekarang cuma "ikut" Master Barang (lewat foreign key), jadi
    // dibungkus method ini supaya kalau Master Barang-nya sudah dihapus dari
    // katalog (soft delete), tetap ada label yang jelas di histori transaksi.
    public function namaBahan(): string
    {
        if (! $this->masterBarang) {
            return '(Barang sudah dihapus dari Master Barang)';
        }

        return $this->masterBarang->nama_barang.($this->masterBarang->trashed() ? ' (sudah dihapus dari katalog)' : '');
    }

    public function satuanBahan(): ?string
    {
        return $this->masterBarang->satuan ?? null;
    }

    public function namaKategori(): string
    {
        return $this->masterBarang->kategori->nama_kategori ?? '-';
    }

    // Label untuk badge asal transaksi (dipakai di Laporan Dapur / detail dapur
    // supaya admin logistik tahu mana yang dibeli sendiri oleh admin dapur).
    public function labelSumber(): string
    {
        return $this->sumber === 'dapur' ? 'Mandiri oleh Dapur' : 'Admin Logistik';
    }
}
