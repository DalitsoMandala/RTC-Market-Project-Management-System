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
        //
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            $table->string('crop')->change();
            $table->string('season_type')->nullable()->change();
$table->integer('household_size')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            $table->string('crop')->change();
            $table->string('season_type')->nullable()->change();
            $table->integer('household_size')->default(0)->change();
        });
    }
};
