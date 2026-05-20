<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isFarmasi(): bool
    {
        return $this->role === 'farmasi';
    }

    public function isKurir(): bool
    {
        return $this->role === 'kurir';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'superadmin' => 'Super Admin',
            'farmasi'    => 'Farmasi',
            'kurir'      => 'Kurir',
            default      => ucfirst($this->role),
        };
    }

    public function courier()
    {
        return $this->hasOne(\App\Models\Courier::class);
    }
}
