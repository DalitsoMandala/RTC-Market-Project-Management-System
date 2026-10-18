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
        // 1. Drop the old foreign key constraint
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
        });

        // 2. Rename the column
        Schema::table('submissions', function (Blueprint $table) {
            $table->renameColumn('period_id', 'submission_period_new_id');
        });

        // 3. Update the IDs using the map table (using the new column name)
        DB::statement("
            UPDATE submissions s
            JOIN submission_period_id_map m ON m.old_id = s.submission_period_new_id
            SET s.submission_period_new_id = m.new_id
        ");

        // 4. Add the new foreign key pointing to the NEW table
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreign('submission_period_new_id')
                ->references('id')
                ->on('submission_periods_new')
                ->onDelete('cascade') // Match your original delete rule
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        // Rollback logic
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['submission_period_new_id']);
        });

        // Map the new IDs back to the old IDs
        DB::statement("
            UPDATE submissions s
            JOIN submission_period_id_map m ON m.new_id = s.submission_period_new_id
            SET s.submission_period_new_id = m.old_id
        ");

        // Rename back
        Schema::table('submissions', function (Blueprint $table) {
            $table->renameColumn('submission_period_new_id', 'period_id');
        });

        // Re-add the old foreign key
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreign('period_id')
                ->references('id')
                ->on('submission_periods')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
