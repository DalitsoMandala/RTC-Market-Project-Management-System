<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Reporting Period Months
        Schema::table('reporting_period_months', function (Blueprint $table) {
            $table->renameColumn('period_id', 'reporting_period_id');
        });
        //Submission Reports
        Schema::table('submission_reports', function (Blueprint $table) {
            $table->renameColumn('period_month_id', 'reporting_period_month_id');
        });

        //Submission Reports
        Schema::table('aggregated_reports', function (Blueprint $table) {
            $table->renameColumn('reporting_period_id', 'reporting_period_month_id');
        });

        //system_reports
        Schema::table('system_reports', function (Blueprint $table) {
            $table->renameColumn('reporting_period_id', 'reporting_period_month_id');
        });

        //map submission periods

        $tables = [
            'attendance_registers',
            'recruitments',
            'rtc_consumptions',
            'rtc_production_farmers',
            'rtc_production_processors',
            'seed_beneficiaries',

            'production_marketing_logs',
        ];
        foreach ($tables as $tableName) {
            // 1. Rename period_month_id to reporting_period_month_id
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('period_month_id', 'reporting_period_month_id');
            });
            // 2. Drop FK for submission_period_id
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['submission_period_id']);
            });
            // 3. Rename submission_period_id to submission_period_new_id
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('submission_period_id', 'submission_period_new_id');
            });
            // 4. Map old IDs to new IDs
            DB::statement("
            UPDATE {$tableName} t
            JOIN submission_period_id_map m ON m.old_id = t.submission_period_new_id
            SET t.submission_period_new_id = m.new_id
        ");
            // 5. Add FK for submission_period_new_id
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('submission_period_new_id')
                    ->references('id')
                    ->on('submission_periods_new')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
