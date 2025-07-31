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
        Schema::create('gross_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->string('batch_type')->default('batch');
            $table->foreignId('submitted_user_id')->constrained('users', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status')->default('pending')->nullable();
            $table->string('table_name');
            $table->string('file_link', 1000)->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_submissions');
    }
};
