<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKondisiInventaris extends Model
{
    protected $table = 'riwayat_kondisi_inventaris';
    protected $fillable = [
        'barang_inventaris_id', 'kondisi_sebelum', 'kondisi_baru',
        'tanggal_update', 'catatan', 'biaya', 'diperbarui_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal_update' => 'date', 'biaya' => 'float'];
    }

    public function barangInventaris(): BelongsTo
    {
        return $this->belongsTo(BarangInventaris::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperbarui_oleh');
    }

    public function namaPengguna(): string
    {
        return $this->pengguna?->namaDanUsername() ?? '(akun sudah dihapus)';
    }
}
