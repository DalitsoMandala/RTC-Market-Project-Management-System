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
        Schema::table('system_reports', function (Blueprint $table) {
            //
            $table->string('crop')->after('indicator_id')->nullable();
        });

        Schema::table('additional_report', function (Blueprint $table) {
            //
            $table->string('crop')->after('indicator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_reports', function (Blueprint $table) {

            $table->dropColumn('crop');
            //
        });

        Schema::table('additional_report', function (Blueprint $table) {
            //
            $table->dropColumn('crop');
        });
    }
};
