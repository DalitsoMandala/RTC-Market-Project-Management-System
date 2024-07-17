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
        Schema::create('reporting_period_months', function (Blueprint $table) {
            $table->id();
            $table->string('start_month');
            $table->string('end_month');
            $table->foreignId('period_id')->constrained('reporting_periods', 'id')->onDelete('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporting_period_months');
    }
};
