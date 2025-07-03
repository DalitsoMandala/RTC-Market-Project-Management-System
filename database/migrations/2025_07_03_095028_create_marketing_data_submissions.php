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
        Schema::create('marketing_data_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->foreignId('submitted_user_id')->constrained('users', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status')->default('pending')->nullable();
            $table->string('table_name');
            $table->string('file_link', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_data_submissions');
    }
};
