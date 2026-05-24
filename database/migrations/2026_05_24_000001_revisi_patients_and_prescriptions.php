<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pasien: hapus birth_date, jadikan nik & rm nullable
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('birth_date');
            $table->string('nik', 16)->nullable()->change();
            $table->string('rm', 20)->nullable()->change();
        });

        // 2. Resep: tambah kolom foto bukti pengiriman
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('delivery_photo')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('name');
            $table->string('nik', 16)->nullable(false)->change();
            $table->string('rm', 20)->nullable(false)->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('delivery_photo');
        });
    }
};
