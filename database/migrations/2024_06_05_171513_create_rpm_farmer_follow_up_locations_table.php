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
        Schema::create('rpm_farmer_follow_up_locations', function (Blueprint $table) {
            $table->id();
            $table->string('enterprise');
            $table->string('district');
            $table->string('epa')->nullable(); // Allow EPA to be null
            $table->string('section');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpm_farmer_follow_up_locations');
    }
};
