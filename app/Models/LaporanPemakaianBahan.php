<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPemakaianBahan extends Model
{
    use HasFactory;

    protected $table = 'laporan_pemakaian_bahan';
    protected $fillable = [
        'bahan_pokok_id', 'status', 'jumlah_sisa', 'jumlah_kurang',
        'tanggal_lapor', 'catatan', 'dilaporkan_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal_lapor' => 'date'];
    }

    public function bahanPokok(): BelongsTo
    {
        return $this->belongsTo(BahanPokok::class);
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'habis' => 'Habis Digunakan',
            'sisa' => 'Masih Ada Sisa',
            'kurang' => 'Bahan Kurang',
            default => ucfirst($this->status),
        };
    }
}
