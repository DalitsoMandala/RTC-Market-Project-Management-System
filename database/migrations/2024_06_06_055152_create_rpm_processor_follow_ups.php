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
        Schema::create('rpm_processor_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpm_processor_id')->constrained('rtc_production_processors', 'id');
            $table->json('location_data');
            $table->date('date_of_follow_up')->nullable();
            $table->json('market_segment')->nullable(); // Multiple market segments (array of strings)
            $table->boolean('has_rtc_market_contract')->default(false);
            $table->decimal('total_vol_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_production_value_previous_season')->nullable(); // MWK
            $table->decimal('total_vol_irrigation_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->json('total_irrigation_production_value_previous_season')->nullable(); // MWK
            $table->boolean('sells_to_domestic_markets')->default(false);
            $table->boolean('sells_to_international_markets')->default(false);
            $table->boolean('uses_market_information_systems')->default(false);
            $table->text('market_information_systems')->nullable();
            $table->json('aggregation_centers')->nullable(); // Stores aggregation center details (array of objects with name and volume sold)
            $table->decimal('aggregation_center_sales', 8, 2);
            // Previous season volume in metric tonnes
           // $table->string('uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpm_processor_follow_ups');
    }
};
