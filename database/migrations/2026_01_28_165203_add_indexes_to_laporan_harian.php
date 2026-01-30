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
            // Index untuk filter by user
            $table->index('user_id');
            // Index untuk sort by tanggal
            $table->index('tanggal_laporan');
            // Index untuk filter by status
            $table->index('status');
            // Composite index untuk user + tanggal
            $table->index(['user_id', 'tanggal_laporan']);
            // Index untuk foreign keys
            $table->index('machine_id');
            $table->index('line_id');
            $table->index('spare_part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['tanggal_laporan']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'tanggal_laporan']);
            $table->dropIndex(['machine_id']);
            $table->dropIndex(['line_id']);
            $table->dropIndex(['spare_part_id']);
        });
    }
};
