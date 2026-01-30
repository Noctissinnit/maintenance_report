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
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('machine_id')->nullable()->constrained('machines');
            $table->foreignId('line_id')->nullable()->constrained('lines');
            $table->foreignId('spare_part_id')->nullable()->constrained('spare_parts');
            $table->string('mesin_name')->nullable();
            $table->string('line')->nullable();
            $table->text('catatan')->nullable();
            $table->string('sparepart')->nullable();
            $table->integer('qty_sparepart')->default(0);
            $table->text('komentar_sparepart')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, running, stopped, completed
            $table->string('jenis_pekerjaan')->nullable(); // corrective, preventive, modifikasi, utility
            $table->string('scope')->nullable(); // Electrik, Mekanik, Utility, Building
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('downtime_min')->default(0);
            $table->string('tipe_laporan')->default('harian'); // harian, mingguan, bulanan
            $table->date('tanggal_laporan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('laporan_harian');
        Schema::enableForeignKeyConstraints();
    }
};
