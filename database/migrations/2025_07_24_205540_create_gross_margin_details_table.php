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
        Schema::create('gross_margin_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gross_margin_id')->constrained('gross_margins')->onDelete('cascade')->onUpdate('cascade');
    $table->string('uuid');
            $table->string('name_of_producer')->nullable();
            $table->string('season')->nullable();
            $table->string('district')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->decimal('gps_s', 65, 7)->nullable();
            $table->decimal('gps_e', 65, 7)->nullable();
            $table->decimal('elevation', 65, 2)->nullable();
            $table->string('type_of_produce')->nullable();
            $table->string('epa')->nullable();
            $table->string('section')->nullable();
            $table->string('ta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_margin_details');
    }
};
