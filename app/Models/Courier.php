<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Courier extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'plate_number',
        'phone',
        'is_active',
        'last_latitude',
        'last_longitude',
        'last_seen_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'last_latitude'  => 'float',
        'last_longitude' => 'float',
        'last_seen_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
