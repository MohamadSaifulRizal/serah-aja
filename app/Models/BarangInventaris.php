<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class BarangInventaris extends Model
{
    use HasFactory;

    protected $table = 'barang_inventaris';
    protected $fillable = [
        'master_barang_id', 'merk', 'dapur_id', 'tanggal_beli',
        'quantity', 'harga_per_item', 'total_harga', 'status_barang', 'kondisi_barang',
        'kondisi_diperbarui_pada', 'kondisi_diperbarui_oleh', 'catatan',
    ];

    protected function casts(): array
    {
        return ['tanggal_beli' => 'date', 'kondisi_diperbarui_pada' => 'date'];
    }

    protected static function booted(): void
    {
        static::saving(function (BarangInventaris $b) {
            $b->total_harga = round($b->quantity * $b->harga_per_item, 2);
        });
    }

    // withTrashed(): sama seperti BahanPokok, supaya histori tetap utuh walau
    // Master Barang-nya sudah "dihapus" (soft delete) dari katalog aktif.
    public function masterBarang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class)->withTrashed();
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function riwayatPerbaikan(): HasMany
    {
        return $this->hasMany(RiwayatPerbaikanInventaris::class);
    }

    public function riwayatKondisi(): HasMany
    {
        return $this->hasMany(RiwayatKondisiInventaris::class)->orderByDesc('tanggal_update')->orderByDesc('id');
    }

    public function pembaruKondisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kondisi_diperbarui_oleh');
    }

    // Total biaya yang dikeluarkan lewat Update Kondisi (perbaikan / beli pengganti).
    public function totalBiayaUpdateKondisi(): float
    {
        return (float) $this->riwayatKondisi->sum('biaya');
    }

    public function totalBiayaPerawatan(): float
    {
        return (float) $this->riwayatPerbaikan()->sum('biaya');
    }

    public function labelStatusBarang(): string
    {
        return $this->status_barang === 'barang_tetap' ? 'Barang Tetap' : 'Barang Habis Pakai';
    }

    public const DAFTAR_KONDISI = [
        'baru' => 'Baru',
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat' => 'Rusak Berat',
    ];

    public static function labelKondisi(?string $kondisi): string
    {
        return self::DAFTAR_KONDISI[$kondisi] ?? '-';
    }

    public function labelKondisiBarang(): string
    {
        return self::labelKondisi($this->kondisi_barang);
    }

    // Satu-satunya jalan mengubah kondisi barang: sekalian mencatat riwayat
    // (kondisi lama -> baru, tanggal, catatan, dan siapa yang mengupdate).
    // $awal = true untuk pencatatan kondisi pertama kali saat barang diinput.
    public function perbaruiKondisi(string $kondisiBaru, ?int $userId, ?string $tanggal = null, ?string $catatan = null, bool $awal = false, float $biaya = 0): void
    {
        $tanggal = $tanggal ?: now()->toDateString();

        DB::transaction(function () use ($kondisiBaru, $userId, $tanggal, $catatan, $awal, $biaya) {
            $this->riwayatKondisi()->create([
                'kondisi_sebelum' => $awal ? null : $this->kondisi_barang,
                'kondisi_baru' => $kondisiBaru,
                'tanggal_update' => $tanggal,
                'catatan' => $catatan,
                'biaya' => $biaya,
                'diperbarui_oleh' => $userId,
            ]);

            $this->update([
                'kondisi_barang' => $kondisiBaru,
                'kondisi_diperbarui_pada' => $tanggal,
                'kondisi_diperbarui_oleh' => $userId,
            ]);
        });
    }

    // Teks "06-10-2026 oleh Budi" untuk kolom/kartu "Terakhir diupdate".
    public function keteranganUpdateKondisi(): string
    {
        if (! $this->kondisi_diperbarui_pada) {
            return 'Belum pernah diupdate';
        }

        return $this->kondisi_diperbarui_pada->format('d-m-Y').' oleh '.($this->pembaruKondisi?->namaDanUsername() ?? '(akun sudah dihapus)');
    }

    // Data untuk popup "Update Kondisi" (dikirim lewat event Alpine). Butuh relasi
    // riwayatKondisi.pengguna sudah di-load supaya tidak N+1.
    public function dataModalKondisi(): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->namaBarang(),
            'dapur' => $this->dapur->nama_dapur ?? '-',
            'kondisi' => $this->kondisi_barang,
            'riwayat' => $this->riwayatKondisi->map(fn (RiwayatKondisiInventaris $r) => [
                'tanggal' => $r->tanggal_update->format('d-m-Y'),
                'dari' => $r->kondisi_sebelum ? self::labelKondisi($r->kondisi_sebelum) : null,
                'ke' => self::labelKondisi($r->kondisi_baru),
                'oleh' => $r->namaPengguna(),
                'catatan' => $r->catatan,
                'biaya' => (float) $r->biaya,
            ])->values()->all(),
        ];
    }

    // Kelas warna badge kondisi (dipakai di tabel Barang Inventaris & halaman Dapur).
    public function kelasBadgeKondisi(): string
    {
        return match ($this->kondisi_barang) {
            'baru' => 'bg-green/20 text-green-deep',
            'baik' => 'bg-green/10 text-green-deep',
            'rusak_ringan' => 'bg-mustard/20 text-mustard',
            'rusak_berat' => 'bg-rust/15 text-rust',
            default => 'bg-paper text-muted',
        };
    }

    // Nama & kategori barang sekarang cuma "ikut" Master Barang - dibungkus
    // method ini supaya tetap ada label jelas walau Master Barang-nya sudah
    // dihapus dari katalog (soft delete).
    public function namaBarang(): string
    {
        if (! $this->masterBarang) {
            return '(Barang sudah dihapus dari Master Barang)';
        }

        return $this->masterBarang->nama_barang.($this->masterBarang->trashed() ? ' (sudah dihapus dari katalog)' : '');
    }

    public function namaKategori(): string
    {
        return $this->masterBarang->kategori->nama_kategori ?? '-';
    }
}
