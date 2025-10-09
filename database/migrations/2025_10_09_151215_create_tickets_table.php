<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_order_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();        // kode unik (uuid/string)
            $table->string('qr_path');               // storage path file PNG
            $table->timestamp('used_at')->nullable();// waktu check-in
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
