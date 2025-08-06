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
        Schema::table('gross_margin_details', function (Blueprint $table) {
            $table->string('selling_price_desc')->nullable();
            $table->decimal('selling_price_qty', 65, 2)->default(0);
            $table->decimal('selling_price_unit_price', 65, 2)->default(0);
            $table->decimal('selling_price', 65, 2)->default(0);
            $table->string('income_price_desc')->nullable();
            $table->decimal('income_price_qty', 65, 2)->default(0);
            $table->decimal('income_price_unit_price', 65, 2)->default(0);
            $table->decimal('income_price', 65, 2)->default(0);
            $table->decimal('total_valuable_costs', 65, 2)->default(0);
            $table->decimal('yield', 65, 2)->default(0);
            $table->decimal('break_even_yield', 65, 2)->default(0);
            $table->decimal('break_even_price', 65, 2)->default(0);
            $table->decimal('gross_margin', 65, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('gross_margin_details', [
            'selling_price_desc',
            'selling_price_qty',
            'selling_price_unit_price',
            'selling_price',
            'income_price_desc',
            'income_price_qty',
            'income_price_unit_price',
            'income_price',
            'total_valuable_costs',
            'yield',
            'break_even_yield',
            'break_even_price',
            'gross_margin',
        ]);
    }
};
