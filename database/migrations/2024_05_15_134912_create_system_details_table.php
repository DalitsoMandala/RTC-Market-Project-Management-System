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
        Schema::create('system_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('website')->nullable();
            $table->string('phone')->nullable(); // New field for phone number
            $table->string('email')->nullable(); // New field for email address
            $table->boolean('maintenance_mode')->default(false); // New field for maintenance mode
            $table->text('maintenance_message')->nullable(); // New field for maintenance message
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_details');
    }
};
