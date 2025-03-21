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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('rc_id')->unique();
            $table->string('epa');
            $table->string('section');
            $table->string('district');
            $table->string('enterprise');
            $table->date('date_of_recruitment')->nullable();
            $table->string('name_of_actor')->nullable();
            $table->string('name_of_representative')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('type')->nullable();
            $table->string('group')->nullable();
            $table->string('approach')->nullable(); // For producer organizations only
            $table->integer('mem_female_18_35')->default(0);
            $table->integer('mem_male_18_35')->default(0);
            $table->integer('mem_male_35_plus')->default(0);
            $table->integer('mem_female_35_plus')->default(0);
            $table->string('sector')->nullable();
            $table->string('category')->nullable();
            $table->enum('establishment_status', ['New', 'Old'])->nullable(); // Uppercase for enum values
            $table->boolean('is_registered')->default(false);
            $table->string('registration_body')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->integer('emp_formal_female_18_35')->default(0);
            $table->integer('emp_formal_male_18_35')->default(0);
            $table->integer('emp_formal_male_35_plus')->default(0);
            $table->integer('emp_formal_female_35_plus')->default(0);
            $table->integer('emp_informal_female_18_35')->default(0);
            $table->integer('emp_informal_male_18_35')->default(0);
            $table->integer('emp_informal_male_35_plus')->default(0);
            $table->integer('emp_informal_female_35_plus')->default(0);
            $table->decimal('area_under_cultivation')->default(0);
            $table->boolean('is_registered_seed_producer')->default(false);
            $table->string('registration_number_seed_producer')->nullable();
            $table->date('registration_date_seed_producer')->nullable();
            $table->boolean('uses_certified_seed')->default(false);
            $table->string('uuid');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('submission_period_id')->constrained('submission_periods', 'id')->onDelete('cascade')->onUpdate('cascade'); // to track changes
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('period_month_id')->constrained('reporting_period_months', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
