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
        Schema::create('rpmf_number_of_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('formal_female_18_35');
            $table->integer('formal_male_18_35');
            $table->integer('formal_male_35_plus');
            $table->integer('formal_female_35_plus');
            $table->integer('informal_female_18_35');
            $table->integer('informal_male_18_35');
            $table->integer('informal_male_35_plus');
            $table->integer('informal_female_35_plus');
            $table->foreignId('rpmf_id')->constrained('rtc_production_farmers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpmf_number_of_employees');
    }
};
