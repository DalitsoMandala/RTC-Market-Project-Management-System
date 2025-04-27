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
            $table->string('pp_id')->unique();
            $table->string('epa')->nullable();
            $table->string('section')->nullable();
            $table->string('district')->nullable();
            $table->string('enterprise')->nullable();
            $table->string('group_name')->nullable();
            $table->date('date_of_followup')->nullable();
            // $table->string('name_of_actor')->nullable();
            // $table->string('name_of_representative')->nullable();
            // $table->string('phone_number')->nullable();
            // $table->string('type')->nullable();
            $table->boolean('market_segment_fresh')->default(false);
            $table->boolean('market_segment_processed')->default(false);
            $table->boolean('has_rtc_market_contract')->default(false);
            //VOLUME IN MT
            $table->decimal('total_vol_production_previous_season', 8, 2)->nullable(); // Metric tonnes
            $table->decimal('total_vol_production_previous_season_produce', 8, 2)->nullable(); // new
            $table->decimal('total_vol_production_previous_season_seed', 8, 2)->nullable(); // new
            $table->decimal('total_vol_production_previous_season_cuttings', 8, 2)->nullable(); // new
            $table->decimal('total_vol_production_previous_season_seed_bundle', 16, 2)->nullable();
            //PRODUCTION IN MWK
            $table->decimal('prod_value_previous_season_total', 16, 2)->nullable();

            $table->decimal('prod_value_previous_season_produce', 16, 2)->nullable();
            $table->decimal('prod_value_previous_season_seed', 16, 2)->nullable();
            $table->decimal('prod_value_previous_season_seed_bundle', 16, 2)->nullable();
            $table->decimal('prod_value_previous_season_cuttings', 16, 2)->nullable();
            $table->decimal('prod_value_produce_prevailing_price', 16, 2)->nullable();
            $table->decimal('prod_value_seed_prevailing_price', 16, 2)->nullable();
            $table->decimal('prod_value_cuttings_prevailing_price', 16, 2)->nullable();
            $table->date('prod_value_previous_season_date_of_max_sales')->nullable();
            //PRODUCTION IN USD
            $table->decimal('prod_value_previous_season_usd_rate', 16, 2)->nullable();
            $table->decimal('prod_value_previous_season_usd_value', 16, 2)->nullable();
            $table->boolean('sells_to_domestic_markets')->default(false);
            $table->boolean('sells_to_international_markets')->default(false);
            $table->boolean('uses_market_information_systems')->default(false);
            $table->boolean('sells_to_aggregation_centers')->default(false);
            $table->decimal('total_vol_aggregation_center_sales', 8, 2)->nullable(); // Previous season volume in metric tonnes
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
