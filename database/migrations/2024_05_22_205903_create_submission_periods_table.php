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
        Schema::create('submission_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms', 'id');
            $table->dateTime('date_established')->nullable();
            $table->dateTime('date_ending')->nullable();
            $table->boolean('is_open')->default(false);
            $table->boolean('is_expired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_periods');
    }
};