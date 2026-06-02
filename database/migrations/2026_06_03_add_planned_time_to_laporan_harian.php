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
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->bigInteger('planned_time_minutes')->nullable()->comment('Planned time in minutes - set manually by operator based on PPIC schedule');
            $table->index('planned_time_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropIndex(['planned_time_minutes']);
            $table->dropColumn('planned_time_minutes');
        });
    }
};
