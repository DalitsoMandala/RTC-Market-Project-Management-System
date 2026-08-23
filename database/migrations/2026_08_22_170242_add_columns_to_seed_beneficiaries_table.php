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
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            //
            $table->integer('child_under_school_fd')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            //
            $table->dropColumn(['child_under_school_fd']);
        });
    }
};
