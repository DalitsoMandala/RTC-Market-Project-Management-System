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
        Schema::create('attendance_registers', function (Blueprint $table) {
            $table->id();
            $table->string('att_id');
            $table->string('meetingTitle');
            $table->string('meetingCategory');
            $table->boolean('rtcCrop_cassava')->default(false);
            $table->boolean('rtcCrop_potato')->default(false);
            $table->boolean('rtcCrop_sweet_potato')->default(false);
            $table->string('venue');
            $table->string('district');
            $table->date('startDate');
            $table->date('endDate');
            $table->integer('totalDays');
            $table->string('name');
            $table->string('sex');
            $table->string('organization')->nullable();
            $table->string('designation')->nullable();
            $table->string('category')->default('partner');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('submission_period_id')->constrained('submission_periods', 'id')->onDelete('cascade')->onUpdate('cascade'); // to track changes
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('period_month_id')->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('uuid');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_registers');
    }
};
