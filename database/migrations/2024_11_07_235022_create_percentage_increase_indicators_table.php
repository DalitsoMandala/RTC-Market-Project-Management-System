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
        Schema::create('percentage_increase_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->nullable()->constrained('financial_years', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('indicator_id')->nullable()->constrained('indicators', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('total_value', 15, 2); // Adjust precision as needed
            $table->decimal('growth_percentage', 10, 2); // Adjust precision as needed
            $table->string('name');
            // Optional: Add foreign key constraints
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percentage_increase_indicators');
    }
};
