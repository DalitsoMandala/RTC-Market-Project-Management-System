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
        Schema::create('rpmp_aggregation_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('rpmp_id')->constrained('rtc_production_processors')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpmp_aggregation_centers');
    }
};
