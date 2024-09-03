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
        Schema::create('hrc_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hrc_id')->constrained('household_rtc_consumption')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('epa');
            $table->string('section');
            $table->string('district');
            $table->string('enterprise');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrc_location');
    }
};
