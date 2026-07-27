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
        Schema::table('aggregated_reports', function (Blueprint $table) {
            $table->unique(
                [
                    'reporting_period_id',
                    'financial_year_id',
                    'organisation_id',
                    'project_id',
                    'indicator_id',
                    'crop',
                ],
                'aggregated_reports_unique_index'
            );
        });

        Schema::table('aggregated_report_data', function (Blueprint $table) {
            $table->unique(
                ['aggregated_report_id', 'name'],
                'aggregated_report_data_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('aggregated_reports', function (Blueprint $table) {
            $table->dropUnique('aggregated_reports_unique_index');
        });

        Schema::table('aggregated_report_data', function (Blueprint $table) {
            $table->dropUnique('aggregated_report_data_unique');
        });
    }
};
