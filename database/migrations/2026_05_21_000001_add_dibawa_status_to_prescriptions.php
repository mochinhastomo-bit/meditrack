<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE prescriptions MODIFY COLUMN status ENUM(
            'penyiapan',
            'siap_kirim',
            'dibawa',
            'dalam_pengiriman',
            'terkirim',
            'dibatalkan'
        ) NOT NULL DEFAULT 'penyiapan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE prescriptions MODIFY COLUMN status ENUM(
            'penyiapan',
            'siap_kirim',
            'dalam_pengiriman',
            'terkirim',
            'dibatalkan'
        ) NOT NULL DEFAULT 'penyiapan'");
    }
};
