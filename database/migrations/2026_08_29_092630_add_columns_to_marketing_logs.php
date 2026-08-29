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
        Schema::table('production_marketing_logs', function (Blueprint $table) {
            //
            $table->string('seed_class')->nullable();
            $table->date('date_recorded')->nullable();
            $table->string('production_value_usd')->default(0)->nullable();
            $table->string('production_value_rate')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_marketing_logs', function (Blueprint $table) {
            //
            $table->dropColumn(['seed_class', 'production_value_usd', 'production_value_rate', 'date_recorded']);
        });
    }
};
