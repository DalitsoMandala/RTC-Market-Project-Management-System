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
        Schema::create('seed_beneficiaries_cassava', function (Blueprint $table) {
            $table->id();
            $table->string('district');
            $table->string('epa');
            $table->string('section');
            $table->string('name_of_aedo');
            $table->string('aedo_phone_number')->nullable();
            $table->date('date');
            $table->string('name_of_recipient');
            $table->string('village')->nullable();
            $table->tinyInteger('sex')->default(1)->nullable(); // 1=Male, 2=Female
            $table->integer('age')->default(0)->nullable();
            $table->tinyInteger('marital_status')->default(1)->nullable(); // 1=Married, 2=Single, 3=Divorced, 4=Widow/er
            $table->tinyInteger('hh_head')->default(1)->nullable(); // 1=MHH, 2=FHH, 3=CHH
            $table->integer('household_size')->default(1)->nullable();
            $table->integer('children_under_5')->default(0)->nullable();
            $table->string('variety_received')->nullable();
            $table->boolean('other')->default(false)->nullable();
            $table->integer('bundles_received')->default(0)->nullable();
            $table->string('national_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('crop', ['OFSP', 'Potato', 'Cassava']); // Can be "OFSP" or "Potato" or "Cassava"
            $table->tinyInteger('signed')->default(0)->nullable();
            $table->string('year')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('submission_period_id')->constrained('submission_periods', 'id')->onDelete('cascade')->onUpdate('cascade'); // to track changes
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('period_month_id')->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->json('more_info')->nullable();
            $table->string('type')->default('NA')->comment('rainfed/rainfall');
            $table->string('uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_beneficiaries_cassava');
    }
};
