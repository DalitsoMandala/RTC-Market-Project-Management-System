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
        Schema::create('rtc_production_farmers', function (Blueprint $table) {
            $table->id();
            $table->json('location_data');
            $table->date('date_of_recruitment');
            $table->string('name_of_actor');
            $table->string('name_of_representative');
            $table->string('phone_number');
            $table->string('type');
            $table->string('approach')->nullable(); // For producer organizations only
            $table->string('sector');
            $table->json('number_of_members')->nullable(); // For producer organizations only
            $table->string('group');
            $table->enum('establishment_status', ['NEW', 'OLD']); // Uppercase for enum values
            $table->boolean('is_registered')->default(false);
            $table->json('registration_details');
            $table->json('number_of_employees')->nullable();
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
            $table->json('market_segment')->nullable(); // Multiple market segments (array of strings)
            $table->boolean('has_rtc_market_contract')->default(false);
            $table->decimal('total_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_production_value_previous_season')->nullable(); // MWK
            $table->decimal('total_irrigation_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_irrigation_production_value_previous_season')->nullable(); // MWK
            $table->boolean('sells_to_domestic_markets')->default(false);
            $table->boolean('sells_to_international_markets')->default(false);
            $table->boolean('uses_market_information_systems')->default(false);
            $table->text('market_information_systems');
            $table->json('aggregation_centers')->nullable(); // Stores aggregation center details (array of objects with name and volume sold)
            $table->decimal('aggregation_center_sales', 8, 2); // Previous season volume in metric tonnes
            $table->string('uuid');
            $table->foreignId('user_id')->constrained('users'); $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtc_production_farmers');
    }
};
