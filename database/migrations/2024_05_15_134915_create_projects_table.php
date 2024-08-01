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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration');
            $table->date('start_date');
            $table->boolean('is_active')->default(false);
            $table->foreignId('cgiar_project_id')->constrained('cgiar_projects', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('reporting_period_id')->constrained('reporting_periods', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};