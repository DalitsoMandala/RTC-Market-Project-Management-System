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
            $table->string('meetingTitle');
            $table->enum('meetingCategory', ['training', 'meeting', 'workshop']);
            $table->json('rtcCrop'); // Adjust based on how you store RTC Crop data
            $table->string('venue');
            $table->string('district');
            $table->date('startDate');
            $table->date('endDate');
            $table->integer('totalDays');
            $table->string('name');
            $table->enum('sex', ['male', 'female']);
            $table->string('organization')->nullable();
            $table->string('designation')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
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