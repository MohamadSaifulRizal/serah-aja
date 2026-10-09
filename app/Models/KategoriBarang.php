<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriBarang extends Model
{
    public const JENIS_BAHAN_POKOK = 'bahan_pokok';
    public const JENIS_INVENTARIS = 'inventaris';

    protected $table = 'kategori_barang';
    protected $fillable = ['nama_kategori', 'jenis'];

    // Kategori dipisah per jenis: "Kategori Bahan Pokok" dan "Kategori Barang
    // Inventaris" (2 menu terpisah di sidebar). Kategori hanya dipakai langsung
    // oleh Master Barang yang berjenis sama.
    public function masterBarang(): HasMany
    {
        return $this->hasMany(MasterBarang::class, 'kategori_id');
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function labelJenis(): string
    {
        return $this->jenis === self::JENIS_INVENTARIS ? 'Barang Inventaris' : 'Bahan Pokok';
    }
}
