<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'farmasi', 'kurir') NOT NULL DEFAULT 'farmasi'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'farmasi') NOT NULL DEFAULT 'farmasi'");
    }
};
