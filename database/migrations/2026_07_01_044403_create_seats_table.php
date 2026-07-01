<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_type_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_kursi'); // 1, 2, ... or S1, S2 for sleeper
            $table->enum('kategori', ['reguler', 'sleeper'])->default('reguler');
            $table->integer('posisi_row');
            $table->integer('posisi_col');
            $table->timestamps();

            $table->unique(['bus_type_id', 'nomor_kursi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
