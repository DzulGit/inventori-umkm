<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aktivitas');
            $table->string('modul');
            $table->text('keterangan')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index('modul');
            $table->index('pengguna_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};