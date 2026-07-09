<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('baggages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pengirim', 100);
            $table->string('no_hp_pengirim', 20)->nullable();
            $table->string('nama_penerima', 100);
            $table->string('no_hp_penerima', 20)->nullable();
            $table->string('jenis_barang', 100);
            $table->string('keterangan', 255)->nullable();
            $table->unsignedInteger('jumlah')->default(1);
            $table->foreignId('diinput_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baggages');
    }
};
