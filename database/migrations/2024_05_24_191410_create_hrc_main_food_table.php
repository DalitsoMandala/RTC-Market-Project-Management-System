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
        Schema::create('hrc_main_food', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['POTATO', 'SWEET POTATO', 'CASSAVA']);
            $table->foreignId('hrc_id')->constrained('household_rtc_consumption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrc_main_food');
    }
};
