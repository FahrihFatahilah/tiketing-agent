<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->string('nama_penumpang');
            $table->string('no_hp')->nullable();
            $table->string('alamat_naik')->nullable();
            $table->string('alamat_turun')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('diinput_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['trip_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
