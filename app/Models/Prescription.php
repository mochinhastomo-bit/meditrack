<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    protected $fillable = [
        'nomor_resep',
        'tanggal',
        'patient_id',
        'patient_address_id',
        'courier_id',
        'keterangan',
        'status',
        'is_active',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONS =====
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(PatientAddress::class, 'patient_address_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }

    // ===== STATUS HELPERS =====
    public static function statusList(): array
    {
        return [
            'penyiapan'        => 'Proses Penyiapan Obat',
            'siap_kirim'       => 'Siap Kirim',
            'dibawa'           => 'Dibawa Kurir',
            'dalam_pengiriman' => 'Dalam Pengiriman',
            'terkirim'         => 'Terkirim',
            'dibatalkan'       => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusList()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'penyiapan'        => 'orange',
            'siap_kirim'       => 'blue',
            'dibawa'           => 'teal',
            'dalam_pengiriman' => 'purple',
            'terkirim'         => 'green',
            'dibatalkan'       => 'red',
            default            => 'gray',
        };
    }

    // ===== AUTO GENERATE NOMOR RESEP =====
    public static function generateNomor(): string
    {
        $prefix = 'RES-' . now()->format('Ymd') . '-';
        $last   = self::where('nomor_resep', 'like', $prefix . '%')
                       ->orderByDesc('nomor_resep')
                       ->value('nomor_resep');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
