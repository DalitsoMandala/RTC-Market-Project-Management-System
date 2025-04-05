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
        Schema::create('farmer_seed_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('rtc_production_farmers','id')->onUpdate('cascade')->onDelete('cascade'); // Replace with actual parent table (e.g., crop_id)
            $table->string('variety');
            $table->date('reg_date')->nullable();
            $table->string('reg_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmer_seed_registrations');
    }
};
