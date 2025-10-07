<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Informasi Dasar Event
            $table->string('nama_event');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('lokasi');

            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();

            $table->unsignedBigInteger('harga_dasar');
            $table->unsignedInteger('kapasitas_total');
            $table->unsignedInteger('stok_tersedia');

            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
