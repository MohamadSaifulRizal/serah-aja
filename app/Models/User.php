<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'dapur_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_LOGISTIK = 'admin_logistik';
    public const ROLE_ADMIN_DAPUR = 'admin_dapur';

    public const DAFTAR_ROLE = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN_LOGISTIK => 'Admin Logistik',
        self::ROLE_ADMIN_DAPUR => 'Admin Dapur',
    ];

    // "Vina (vina)" - nama + username akun, dipakai untuk menandai siapa yang melakukan suatu aksi.
    public function namaDanUsername(): string
    {
        return $this->name.' ('.$this->username.')';
    }

    public function labelRole(): string
    {
        return self::DAFTAR_ROLE[$this->role] ?? $this->role;
    }

    // Halaman awal setelah login / saat ditolak akses, sesuai role.
    public function rutePerdana(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN_DAPUR => route('dapur.beranda'),
            default => route('dashboard'),
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminLogistik(): bool
    {
        return $this->role === 'admin_logistik';
    }

    public function isAdminDapur(): bool
    {
        return $this->role === 'admin_dapur';
    }
}
