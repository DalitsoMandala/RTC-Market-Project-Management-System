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
        Schema::table('marketing_data', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });

         Schema::table('submissions', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });

          Schema::table('gross_submissions', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });

          Schema::table('submissions', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });

          Schema::table('root_tuber_submissions', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });

          Schema::table('progress_submissions', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_data', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

         Schema::table('submissions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

          Schema::table('gross_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

          Schema::table('submissions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

          Schema::table('root_tuber_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

          Schema::table('progress_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });

    }
};
