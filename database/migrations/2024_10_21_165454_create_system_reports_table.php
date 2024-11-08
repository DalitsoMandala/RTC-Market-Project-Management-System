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
        Schema::create('system_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporting_period_id')->nullable()->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade'); // to track changes
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->nullable()->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('indicator_id')->nullable()->constrained('indicators', 'id')->onDelete('cascade')->onUpdate('cascade');
            // $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_reports');
    }
};
