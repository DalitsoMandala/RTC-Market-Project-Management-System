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
        //
        Schema::create('submission_periods_new', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_established')->nullable();
            $table->dateTime('date_ending')->nullable();
            $table->foreignId('month_range_period_id')
                ->constrained('reporting_period_months', 'id')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('financial_year_id')
                ->constrained('financial_years', 'id')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_open')->default(false);
            $table->boolean('is_expired')->default(false);
            $table->boolean('is_restricted')->default(false);
            $table->boolean('bypass_notification')->default(false);
            $table->json('exception_list')->nullable(); // copied, not part of identity
            $table->timestamps();

            $table->unique(
                [
                    'date_established', 'date_ending',
                    'is_open', 'is_expired',
                    'financial_year_id', 'month_range_period_id',
                    'is_restricted', 'bypass_notification',
                ],
                'submission_periods_new_unique'
            );
        });

        DB::statement(<<<SQL
    INSERT INTO submission_periods_new
        (date_established, date_ending,
         is_open, is_expired, is_restricted, bypass_notification,
         financial_year_id, month_range_period_id,
         exception_list, created_at, updated_at)
    SELECT
        date_established, date_ending,
        is_open, is_expired, is_restricted, bypass_notification,
        financial_year_id, month_range_period_id,
        ANY_VALUE(exception_list),   -- null today; arbitrary if ever set
        MIN(created_at),
        MAX(updated_at)
    FROM submission_periods
    GROUP BY
        date_established, date_ending,
        is_open, is_expired, is_restricted, bypass_notification,
        financial_year_id, month_range_period_id
SQL);

        Schema::create('submission_period_id_map', function (Blueprint $table) {
            $table->unsignedBigInteger('old_id')->primary();
            $table->unsignedBigInteger('new_id')->index();
        });

        DB::statement(<<<SQL
    INSERT INTO submission_period_id_map (old_id, new_id)
    SELECT o.id, n.id
    FROM submission_periods o
    JOIN submission_periods_new n
      ON (n.date_established      <=> o.date_established)
     AND (n.date_ending           <=> o.date_ending)
     AND (n.is_open               <=> o.is_open)
     AND (n.is_expired            <=> o.is_expired)
     AND (n.is_restricted         <=> o.is_restricted)
     AND (n.bypass_notification   <=> o.bypass_notification)
     AND (n.financial_year_id     <=> o.financial_year_id)
     AND (n.month_range_period_id <=> o.month_range_period_id)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('submission_periods_new');
        Schema::dropIfExists('submission_period_id_map');
    }
};
