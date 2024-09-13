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
        Schema::create('rpmf_area_cultivation', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('variety')->nullable();
            $table->foreignId('rpmf_id')->constrained('rtc_production_farmers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpmf_area_cultivation');
    }
};
