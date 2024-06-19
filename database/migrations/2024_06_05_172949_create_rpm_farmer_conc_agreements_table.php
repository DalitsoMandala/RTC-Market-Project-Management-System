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
        Schema::create('rpm_farmer_conc_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpm_farmer_id')->constrained('rtc_production_farmers', 'id');
            $table->date('date_recorded');
            $table->string('partner_name');
            $table->string('country');
            $table->date('date_of_maximum_sale')->nullable();
            $table->enum('product_type', ['SEED', 'WARE', 'VALUE ADDED PRODUCTS']);
            $table->decimal('volume_sold_previous_period', 8, 2)->nullable(); // Metric tonnes
            $table->decimal('financial_value_of_sales', 18, 2); // Malawi Kwacha

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpm_farmer_conc_agreements');
    }
};
