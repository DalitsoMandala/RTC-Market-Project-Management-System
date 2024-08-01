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
        Schema::create('assigned_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_target_id')->constrained('indicator_targets', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organisation_id')->constrained('organisations', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('target_value');
            $table->integer('current_value')->default(0);
            $table->json('detail')->nullable();
            $table->enum('type', ['number', 'percentage', 'detail'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_targets');
    }
};
