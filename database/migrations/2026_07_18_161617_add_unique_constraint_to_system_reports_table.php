<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_reports', function (Blueprint $table) {
            $table->unique(
                [
                    'reporting_period_id',
                    'financial_year_id',
                    'organisation_id',
                    'project_id',
                    'indicator_id',
                    'crop',
                ],
                'system_reports_unique_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('system_reports', function (Blueprint $table) {
            $table->dropUnique('system_reports_unique_index');
        });
    }
};
