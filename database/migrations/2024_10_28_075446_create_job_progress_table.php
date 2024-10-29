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
        Schema::create('job_progress', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->string('form_name')->nullable();
            $table->unsignedInteger('total_rows')->default(0); // Total rows for combined sheets
            $table->unsignedInteger('processed_rows')->default(0); // Rows processed so far
            $table->unsignedTinyInteger('progress')->default(0); // Percentage progress
            $table->enum('status', ['processing',  'completed', 'failed'])->default('processing');
            $table->foreignId('user_id')->constrained('users');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_progress');
    }
};
