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
            $table->foreignId('rpm_processor_id')->constrained('rtc_production_processors', 'id')->onDelete('cascade')->onUpdate('cascade');

            $table->date('date_of_follow_up')->nullable();
            $table->boolean('market_segment_fresh')->default(false);
            $table->boolean('market_segment_processed')->default(false);
            $table->boolean('has_rtc_market_contract')->default(false)->nullable();
            $table->decimal('total_vol_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            //    $table->json('total_production_value_previous_season')->nullable();
            $table->decimal('prod_value_previous_season_total', 16, 2)->nullable();
            $table->date('prod_value_previous_season_date_of_max_sales')->nullable();
            $table->decimal('prod_value_previous_season_usd_rate', 16, 2)->nullable();
            $table->decimal('prod_value_previous_season_usd_value', 16, 2)->nullable();

            $table->boolean('sells_to_domestic_markets')->default(false)->nullable();
            $table->boolean('sells_to_international_markets')->default(false)->nullable();
            $table->boolean('uses_market_information_systems')->default(false)->nullable();


            $table->boolean('sells_to_aggregation_centers')->default(false);
            // $table->json('aggregation_centers')->nullable(); // Stores aggregation center details (array of objects with name and volume sold)
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
        Schema::dropIfExists('rpm_processor_follow_ups');
    }
};
