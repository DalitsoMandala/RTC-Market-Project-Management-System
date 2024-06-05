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
        Schema::create('school_rtc_consumption', function (Blueprint $table) {
            $table->id();
            $table->enum('crop', ['CASSAVA', 'POTATO', 'SWEET POTATO'])->nullable();
            $table->integer('male_count');
            $table->integer('female_count');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_rtc_consumption');
    }
};
