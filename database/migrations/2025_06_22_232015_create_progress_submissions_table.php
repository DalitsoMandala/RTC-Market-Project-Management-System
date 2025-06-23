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
        Schema::create('progress_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->foreignId('submitted_user_id')->constrained('users', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('report_organisation_id')->constrained('organisations', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status')->default('active')->nullable();
            $table->string('table_name');
            $table->string('file_link', 1000)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_submissions');
    }
};
