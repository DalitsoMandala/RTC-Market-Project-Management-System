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
        Schema::table('recruitments', function (Blueprint $table) {

            $table->integer('profit_female_18_35')->default(0);
            $table->integer('profit_male_18_35')->default(0);
            $table->integer('profit_male_35_plus')->default(0);
            $table->integer('profit_female_35_plus')->default(0);
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropColumn(['profit_female_18_35', 'profit_male_18_35', 'profit_male_35_plus', 'profit_female_35_plus']);
        });
    }
};
