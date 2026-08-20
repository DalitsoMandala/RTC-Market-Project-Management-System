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
        Schema::create('production_marketing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('prod_market_id');
            $table->string('district')->nullable();
            $table->string('epa')->nullable();
            $table->string('section')->nullable();
            $table->string('enterprise')->nullable();
            $table->string('group_name')->nullable();

            $table->string('type_of_farming')->nullable();
            $table->string('season')->nullable();

            $table->string('group_chair_name')->nullable();
            $table->string('group_chair_contact')->nullable();

            $table->string('farmer_name')->nullable();
            $table->string('farmer_id_phone')->nullable();

            $table->string('sex')->nullable();
            $table->unsignedInteger('age')->nullable();

            $table->decimal('area_grown_acre', 10, 2)->nullable();

            $table->string('variety')->nullable();

            $table->string('harvesting_units')->nullable();
            $table->decimal('unit_weight_kg', 12, 2)->nullable();
            $table->decimal('qty', 15, 2)->nullable();

            $table->decimal('selling_price', 15, 2)->nullable();

            $table->string('main_buyer')->nullable();
            $table->string('uuid');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('submission_period_id')->constrained('submission_periods', 'id')->onDelete('cascade')->onUpdate('cascade'); // to track changes
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('period_month_id')->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_marketing_logs');
    }
};
