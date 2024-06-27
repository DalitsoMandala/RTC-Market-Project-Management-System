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
        Schema::create('indicator_sub_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_target_id')->constrained('indicator_targets', 'id');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id');
            $table->json('target')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_sub_targets');
    }
};