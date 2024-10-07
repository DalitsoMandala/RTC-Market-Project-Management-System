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
        Schema::create('submission_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('month_range_period_id')->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('indicator_id')->constrained('indicators', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('target_name')->nullable(); // E.g., Cassava, Potato, etc.
            $table->decimal('target_value', 10, 2)->nullable(); // E.g., 100 (metric tonnes or whatever unit)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_targets');
    }
};
