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
            $table->string('epa');
            $table->string('section');
            $table->string('district');
            $table->string('enterprise');
            $table->date('date_of_assessment')->nullable();
            $table->string('actor_type')->nullable();
            $table->string('rtc_group_platform')->nullable(); // Allow null for RTC group/platform
            $table->string('producer_organisation')->nullable(); // Allow null for producer organization
            $table->string('actor_name')->nullable();
            $table->string('age_group')->nullable();
            $table->string('sex')->nullable();
            $table->string('phone_number')->nullable(); // Allow null for phone number
            $table->integer('household_size')->nullable();
            $table->integer('under_5_in_household')->nullable();
            $table->integer('rtc_consumers')->nullable();
            $table->integer('rtc_consumers_potato')->nullable();
            $table->integer('rtc_consumers_sw_potato')->nullable();
            $table->integer('rtc_consumers_cassava')->nullable();
            $table->integer('rtc_consumption_frequency')->nullable(); // Limit to positive values
            $table->string('uuid');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('household_rtc_consumption');
    }
};
