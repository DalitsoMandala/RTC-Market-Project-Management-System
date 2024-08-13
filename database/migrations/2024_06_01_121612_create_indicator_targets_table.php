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
        Schema::create('indicator_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('indicators', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_id')->constrained('projects', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('target_value')->nullable();
            $table->integer('baseline_value')->nullable();
            $table->enum('type', ['number', 'percentage', 'detail'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_targets');
    }
};