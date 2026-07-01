<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bus_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Skyclass / Legrest
            $table->integer('total_seat');
            $table->json('layout_config'); // grid layout definition
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_types');
    }
};
