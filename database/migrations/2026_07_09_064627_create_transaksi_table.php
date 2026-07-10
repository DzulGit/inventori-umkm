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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->dateTime('tanggal_transaksi');
            $table->foreignId('pengguna_id')->constrained('users')->restrictOnDelete();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('tanggal_transaksi');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
