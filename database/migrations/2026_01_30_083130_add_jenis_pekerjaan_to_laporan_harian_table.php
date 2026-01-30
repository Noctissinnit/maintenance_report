<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->string('jenis_pekerjaan')->default('corrective')->after('status');
        });

        // Rename columns for clarity - using raw SQL to handle the rename better
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'waktu_mulai')) {
                DB::statement('ALTER TABLE laporan_harian CHANGE COLUMN waktu_mulai start_time DATETIME NULL');
            }
            if (Schema::hasColumn('laporan_harian', 'waktu_selesai')) {
                DB::statement('ALTER TABLE laporan_harian CHANGE COLUMN waktu_selesai end_time DATETIME NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_harian', 'jenis_pekerjaan')) {
                $table->dropColumn('jenis_pekerjaan');
            }
            if (Schema::hasColumn('laporan_harian', 'start_time')) {
                $table->renameColumn('start_time', 'waktu_mulai');
            }
            if (Schema::hasColumn('laporan_harian', 'end_time')) {
                $table->renameColumn('end_time', 'waktu_selesai');
            }
        });
    }
};
