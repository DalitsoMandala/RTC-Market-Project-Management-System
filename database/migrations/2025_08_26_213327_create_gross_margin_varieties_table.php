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
        Schema::create('gross_margin_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gross_margin_id')->constrained('gross_margins', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('gross_margin_category_id')->constrained('gross_margin_categories', 'id')->onDelete('cascade')->onUpdate('cascade');

            $table->string('variety')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('qty', 12, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total', 16, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_margin_varieties');
    }
};
