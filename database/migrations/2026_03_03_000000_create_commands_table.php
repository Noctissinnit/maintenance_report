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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_head_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('command_text');
            $table->longText('action_plan');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('supervisor_notes')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index('department_head_id');
            $table->index('supervisor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
