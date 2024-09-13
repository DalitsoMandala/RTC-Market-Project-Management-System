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
        Schema::create('rpmf_market_segment', function (Blueprint $table) {
            $table->id();
            $table->boolean('fresh')->default(false);
            $table->boolean('segment')->default(false);
            $table->foreignId('rpmf_id')->constrained('rtc_production_farmers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpmf_market_segment');
    }
};
