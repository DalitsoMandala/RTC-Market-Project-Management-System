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
        Schema::create('cassava_tots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Name of the participant
            $table->string('gender')->nullable(); // Gender of the participant
            $table->string('age_group')->nullable(); // Age group of the participant
            $table->string('district')->nullable(); // District of the participant
            $table->string('epa')->nullable(); // EPA of the participant
            $table->string('position')->nullable(); // Position of the participant (nullable)
            $table->string('phone_numbers')->nullable(); // Phone numbers of the participant (nullable)
            $table->string('email_address')->nullable(); // Email address of the participant (nullable)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cassava_tots');
    }
};