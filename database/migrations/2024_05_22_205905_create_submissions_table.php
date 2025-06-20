<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->foreignId('form_id')->constrained('forms', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('period_id')->constrained('submission_periods', 'id')->onDelete('cascade')->onUpdate('cascade'); // submission_period not month report period
            $table->foreignId('user_id')->constrained('users', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->json('data')->nullable();
            $table->enum('batch_type', ['batch', 'manual', 'aggregate', 'missing-report'])->default('manual');
            $table->boolean('is_complete')->default(false);
            $table->string('table_name');
            $table->string('file_link', 1000)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            //  $table->softDeletes();
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
