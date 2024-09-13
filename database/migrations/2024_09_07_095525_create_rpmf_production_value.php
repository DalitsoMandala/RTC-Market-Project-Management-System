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
        Schema::create('rpmf_production_value', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 16, 2)->nullable();
            $table->date('date_of_max_sales')->nullable();
            $table->decimal('usd_rate', 16, 2)->nullable();
            $table->decimal('usd_value', 16, 2)->nullable();
            $table->foreignId('rpmf_id')->constrained('rtc_production_farmers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpmf_production_value');
    }
};
