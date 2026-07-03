<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropUnique(['schedule_id', 'tanggal_berangkat']);
            $table->foreign('schedule_id')->references('id')->on('schedules')->cascadeOnDelete();
            $table->unique(['bus_id', 'schedule_id', 'tanggal_berangkat']);
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropUnique(['bus_id', 'schedule_id', 'tanggal_berangkat']);
            $table->foreign('schedule_id')->references('id')->on('schedules')->cascadeOnDelete();
            $table->unique(['schedule_id', 'tanggal_berangkat']);
        });
    }
};
