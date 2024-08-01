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
        Schema::create('target_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_target_id')->constrained('indicator_targets', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->integer('target_value');
            $table->enum('type', ['number', 'percentage'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_details');
    }
};