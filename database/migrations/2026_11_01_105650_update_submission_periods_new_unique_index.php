
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_periods_new', function (Blueprint $table) {
            $table->dropUnique('submission_periods_new_unique');

            $table->unique(
                ['financial_year_id', 'reporting_period_month_id'],
                'submission_periods_new_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('submission_periods_new', function (Blueprint $table) {
            $table->dropUnique('submission_periods_new_unique');

            $table->unique([
                'date_established',
                'date_ending',
                'is_open',
                'is_expired',
                'financial_year_id',
                'reporting_period_month_id',
                'is_restricted',
                'bypass_notification',
            ], 'submission_periods_new_unique');
        });
    }
};
