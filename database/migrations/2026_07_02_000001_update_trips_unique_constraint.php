<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropUnique(['schedule_id', 'tanggal_berangkat']);
            $table->unique(['bus_id', 'schedule_id', 'tanggal_berangkat']);
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropUnique(['bus_id', 'schedule_id', 'tanggal_berangkat']);
            $table->unique(['schedule_id', 'tanggal_berangkat']);
        });
    }
};
