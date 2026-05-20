<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'nik',
        'rm',
        'name',
        'birth_date',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active'  => 'boolean',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(PatientAddress::class);
    }

    public function primaryAddress()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }
}
