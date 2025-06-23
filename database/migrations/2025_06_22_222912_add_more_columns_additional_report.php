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
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('batch_type', ['batch', 'manual', 'aggregate', 'progress-summary'])->default('manual')->change();
        });
        Schema::table('additional_report', function (Blueprint $table) {


            $table->decimal('y1_target', 15, 2)->default(0)->after('year_1');
            $table->decimal('year_3', 15, 2)->default(0)->after('y2_target');
            $table->decimal('y3_target', 15, 2)->default(0)->after('year_3');
            $table->decimal('year_4', 15, 2)->default(0)->after('y3_target');
            $table->decimal('y4_target', 15, 2)->default(0)->after('year_4');
            $table->string('status')->default('active')->nullable()->after('organisation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('batch_type', ['batch', 'manual', 'aggregate'])->default('manual')->change();
        });
        //
        Schema::table('additional_report', function (Blueprint $table) {
            $table->dropColumn('y1_target');
            $table->dropColumn('year_3');
            $table->dropColumn('y3_target');
            $table->dropColumn('year_4');
            $table->dropColumn('y4_target');
            $table->dropColumn('status');
        });
    }
};
