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
        Schema::create('household_rtc_consumption', function (Blueprint $table) {
            $table->id();

            $table->json('location_data')->nullable();
            $table->date('date_of_assessment')->nullable();
            $table->enum('actor_type', ['FARMER', 'PROCESSOR', 'TRADER', 'INDIVIDUALS FROM NUTRITION INTERVENTION', 'OTHER'])->nullable();
            $table->string('rtc_group_platform')->nullable(); // Allow null for RTC group/platform
            $table->string('producer_organisation')->nullable(); // Allow null for producer organization
            $table->string('actor_name')->nullable();
            $table->enum('age_group', ['YOUTH', 'NOT YOUTH'])->nullable();
            $table->enum('sex', ['MALE', 'FEMALE'])->nullable();
            $table->string('phone_number')->nullable(); // Allow null for phone number
            $table->integer('household_size')->nullable();
            $table->integer('under_5_in_household')->nullable();
            $table->integer('rtc_consumers')->nullable();
            $table->integer('rtc_consumers_potato')->nullable();
            $table->integer('rtc_consumers_sw_potato')->nullable();
            $table->integer('rtc_consumers_cassava')->nullable();
            $table->integer('rtc_consumption_frequency')->nullable(); // Limit to positive values
            $table->json('main_food_data')->nullable();
            $table->string('uuid');
            $table->foreignId('period_id')->constrained('submission_periods', 'id'); // to track changes
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('household_rtc_consumption');
    }
};