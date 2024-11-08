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
        Schema::create('seed_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('district');
            $table->string('epa');
            $table->string('section');
            $table->string('name_of_aedo');
            $table->string('aedo_phone_number');
            $table->date('date');
            $table->string('name_of_recipient');
            $table->string('village');
            $table->tinyInteger('sex'); // 1=Male, 2=Female
            $table->integer('age');
            $table->tinyInteger('marital_status'); // 1=Married, 2=Single, 3=Divorced, 4=Widow/er
            $table->tinyInteger('hh_head'); // 1=MHH, 2=FHH, 3=CHH
            $table->integer('household_size');
            $table->integer('children_under_5');
            $table->string('variety_received');
            $table->integer('bundles_received');
            $table->string('phone_or_national_id');
            $table->string('crop'); // Can be "OFSP" or "Potato" or "Cassava"
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_beneficiaries');
    }
};
