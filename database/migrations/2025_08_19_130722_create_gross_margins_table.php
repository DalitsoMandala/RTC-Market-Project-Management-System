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
        Schema::create('gross_margins', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('name');
            $table->string('sex')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('date')->nullable();
            $table->string('district')->nullable();
            $table->string('ta')->nullable();
            $table->string('village')->nullable();
            $table->string('epa')->nullable();
            $table->string('section')->nullable();
            $table->string('gps_s')->nullable();
            $table->string('gps_e')->nullable();
            $table->string('elevation')->nullable();
            $table->string('enterprise')->nullable();
            $table->string('type_of_produce')->nullable(); // seed/ware/cuttings
            $table->string('season')->nullable(); // rainfed/irrigated
            $table->decimal('total_variable_cost', 15, 2)->default(0);
            $table->decimal('total_harvest', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('income', 15, 2)->default(0);
            $table->decimal('yield', 15, 2)->default(0);
            $table->decimal('break_even_yield', 15, 2)->default(0);
            $table->decimal('break_even_price', 15, 2)->default(0);
            $table->decimal('gross_margin', 15, 2)->default(0);
              $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_margins');
    }
};
