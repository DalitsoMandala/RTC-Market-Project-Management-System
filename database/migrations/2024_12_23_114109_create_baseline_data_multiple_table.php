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
        Schema::create('baseline_data_multiple', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baseline_data_id')->constrained('baseline_data', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('indicator_id')->constrained('indicators', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('baseline_value', 16, 2)->default(0.00);
            $table->string('name');
            $table->string('unit_type')->default('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baseline_data_multiple');
    }
};