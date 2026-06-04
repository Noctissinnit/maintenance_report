<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planned_times', function (Blueprint $table) {
            $table->dropUnique('planned_times_year_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_times', function (Blueprint $table) {
            $table->unique(['year', 'month']);
        });
    }
};
