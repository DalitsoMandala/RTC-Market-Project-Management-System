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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->foreignId('form_id')->constrained('forms', 'id');
            $table->foreignId('period_id')->constrained('submission_periods', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->json('data');
            $table->enum('batch_type', ['batch', 'manual', 'aggregate'])->default('manual');
            $table->boolean('is_complete')->default(false);
            $table->string('table_name')->unique();
            $table->string('file_link')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
