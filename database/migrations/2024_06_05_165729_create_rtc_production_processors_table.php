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
        Schema::create('rtc_production_processors', function (Blueprint $table) {
            $table->id();
            $table->json('location_data');
            $table->date('date_of_recruitment')->nullable();
            $table->string('name_of_actor')->nullable();
            $table->string('name_of_representative')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('type')->nullable();
            $table->string('approach')->nullable(); // For producer organizations only
            $table->string('sector')->nullable();
            $table->json('number_of_members')->nullable(); // For producer organizations only
            $table->string('group')->nullable();
            $table->enum('establishment_status', ['NEW', 'OLD']); // Uppercase for enum values
            $table->boolean('is_registered')->default(false);
            $table->json('registration_details')->nullable();
            $table->json('number_of_employees')->nullable();

            $table->json('market_segment')->nullable(); // Multiple market segments (array of strings)
            $table->boolean('has_rtc_market_contract')->default(false);
            $table->decimal('total_vol_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_production_value_previous_season')->nullable(); // MWK
            $table->decimal('total_vol_irrigation_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_irrigation_production_value_previous_season')->nullable(); // MWK
            $table->boolean('sells_to_domestic_markets')->default(false);
            $table->boolean('sells_to_international_markets')->default(false);
            $table->boolean('uses_market_information_systems')->default(false);
            $table->json('market_information_systems')->nullable();
            $table->boolean('sells_to_aggregation_centers')->default(false);
            $table->json('aggregation_centers')->nullable(); // Stores aggregation center details (array of objects with name and volume sold)
            $table->decimal('total_vol_aggregation_center_sales', 8, 2)->nullable(); // Previous season volume in metric tonnes
            // Previous season volume in metric tonnes
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
        Schema::dropIfExists('rtc_production_processors');
    }
};
