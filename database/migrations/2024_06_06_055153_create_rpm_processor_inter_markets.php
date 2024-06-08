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
        Schema::create('rpm_processor_inter_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpm_processor_id')->constrained('rtc_production_processors', 'id');
            $table->date('date_recorded');
            $table->enum('crop_type', ['CASSAVA', 'POTATO', 'SWEET POTATO']);
            $table->string('market_name');
            $table->string('country');
            $table->date('date_of_maximum_sale')->nullable();
            $table->enum('product_type', ['SEED', 'WARE', 'VALUE ADDED PRODUCTS']);
            $table->decimal('volume_sold_previous_period', 8, 2)->nullable(); // Metric tonnes (optional)
            $table->decimal('financial_value_of_sales', 18, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpm_processor_inter_markets');
    }
};
