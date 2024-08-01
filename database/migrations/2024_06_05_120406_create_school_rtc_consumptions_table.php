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
        Schema::create('school_rtc_consumption', function (Blueprint $table) {
            $table->id();
            $table->json('location_data')->nullable();
            $table->date('date')->nullable();
            $table->enum('crop', ['CASSAVA', 'POTATO', 'SWEET POTATO'])->nullable();
            $table->integer('male_count')->nullable();
            $table->integer('female_count')->nullable();
            $table->integer('total')->nullable();
            $table->string('uuid');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'denied', 'approved'])->default('pending');
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