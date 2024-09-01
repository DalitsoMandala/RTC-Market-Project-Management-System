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
        Schema::create('rpm_farmer_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpm_farmer_id')->constrained('rtc_production_farmers', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->json('location_data')->nullable();
            $table->date('date_of_follow_up')->nullable();

            // Production related columns
            $table->json('area_under_cultivation')->nullable(); // Stores area by variety (key-value pairs)
            $table->json('number_of_plantlets_produced')->nullable();
            $table->integer('number_of_screen_house_vines_harvested')->nullable(); // Sweet potatoes
            $table->integer('number_of_screen_house_min_tubers_harvested')->nullable(); // Potatoes
            $table->integer('number_of_sah_plants_produced')->nullable(); // Cassava
            $table->json('area_under_basic_seed_multiplication')->nullable(); // Acres
            $table->json('area_under_certified_seed_multiplication')->nullable(); // Acres
            $table->boolean('is_registered_seed_producer')->default(false);
            $table->json('seed_service_unit_registration_details')->nullable();
            $table->boolean('uses_certified_seed')->default(false);
            // Marketing columns
            $table->json('market_segment')->nullable(); // Multiple market segments (array of strings)

            $table->boolean('has_rtc_market_contract')->default(false)->nullable();
            $table->decimal('total_vol_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_production_value_previous_season')->nullable(); // MWK
            $table->boolean('sells_to_domestic_markets')->default(false)->nullable();
            $table->boolean('sells_to_international_markets')->default(false)->nullable();
            $table->boolean('uses_market_information_systems')->default(false)->nullable();
            $table->text('market_information_systems')->nullable();

            $table->boolean('sells_to_aggregation_centers')->default(false);
            $table->json('aggregation_centers')->nullable(); // Stores aggregation center details (array of objects with name and volume sold)
            $table->decimal('total_vol_aggregation_center_sales', 8, 2)->nullable(); // Previous season volume in metric tonnes
            $table->foreignId('user_id')->constrained('users');

            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpm_farmer_follow_ups');
    }
};
