<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_resep', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained()->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'penyiapan',
                'siap_kirim',
                'dalam_pengiriman',
                'terkirim',
                'dibatalkan',
            ])->default('penyiapan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
