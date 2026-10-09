<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerbaikanInventaris extends Model
{
    use HasFactory;

    protected $table = 'riwayat_perbaikan_inventaris';
    protected $fillable = ['barang_inventaris_id', 'tanggal', 'jenis_aksi', 'biaya', 'vendor', 'catatan'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function barangInventaris(): BelongsTo
    {
        return $this->belongsTo(BarangInventaris::class);
    }

    public function labelJenisAksi(): string
    {
        return match ($this->jenis_aksi) {
            'perawatan' => 'Perawatan Rutin',
            'perbaikan' => 'Perbaikan',
            'penggantian' => 'Penggantian Suku Cadang',
            default => ucfirst($this->jenis_aksi),
        };
    }
}
