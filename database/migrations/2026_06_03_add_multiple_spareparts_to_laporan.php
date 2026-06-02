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
            // Add JSON column for storing multiple spare parts
            // Structure: [{ id: 1, name: 'Part Name', qty: 5, komentar: 'text' }, ...]
            $table->json('spare_parts_used')->nullable()->after('komentar_sparepart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropColumn('spare_parts_used');
        });
    }
};
