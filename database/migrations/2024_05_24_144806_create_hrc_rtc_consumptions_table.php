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
        Schema::create('hrc_rtc_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hrc_id')->constrained('household_rtc_consumption');
            $table->boolean('total'); // Consumes any RTC product
            $table->boolean('cassava');
            $table->boolean('potato');
            $table->boolean('sweet_potato');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrc_rtc_consumptions');
    }
};
