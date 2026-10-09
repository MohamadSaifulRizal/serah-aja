<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $fillable = ['nama_supplier', 'kontak_supplier', 'alamat'];

    public function bahanPokok(): HasMany
    {
        return $this->hasMany(BahanPokok::class);
    }
}
