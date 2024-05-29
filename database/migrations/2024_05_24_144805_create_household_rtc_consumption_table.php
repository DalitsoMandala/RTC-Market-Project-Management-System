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
        Schema::create('household_rtc_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('hrc_locations'); // Link to locations table
            $table->date('date_of_assessment');
            $table->enum('actor_type', ['FARMER', 'PROCESSOR', 'TRADER', 'INDIVIDUALS FROM NUTRITION INTERVENTION', 'OTHER']);
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
            $table->tinyInteger('rtc_consumption_frequency')->unsigned(); // Limit to positive values
            $table->string('uuid');
            $table->string('file_link')->nullable();
            $table->foreignId('user_id')->constrained('users');
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