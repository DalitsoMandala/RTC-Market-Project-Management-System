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
        Schema::create('aggregated_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('reporting_period_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('value')->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aggregated_reports');
    }
};
