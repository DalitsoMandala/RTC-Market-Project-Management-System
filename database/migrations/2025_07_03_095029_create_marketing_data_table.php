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
        Schema::create('marketing_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('marketing_data_submissions', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->date('entry_date')->nullable(); // full date of record
            $table->string('off_taker_name')->nullable();
            $table->string('vehicle_reg_number')->nullable();
            $table->string('trader_contact')->nullable();
            $table->string('buyer_location')->nullable();
            $table->string('variety_demanded')->nullable();
            $table->string('quality_size')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('units')->nullable();
            $table->decimal('estimated_demand_kg', 10, 2)->nullable();
            $table->decimal('agreed_price_per_kg', 12, 2)->nullable(); // in MWK
            $table->string('market_ordered_from')->nullable();
            $table->string('final_market')->nullable();
            $table->string('final_market_district')->nullable();
            $table->string('final_market_country')->nullable();
            $table->string('supply_frequency')->nullable();
            $table->decimal('estimated_total_value_mk', 15, 2)->nullable();
            $table->decimal('estimated_total_value_usd', 15, 2)->nullable();
            $table->string('status')->default('pending')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_data');
    }
};
