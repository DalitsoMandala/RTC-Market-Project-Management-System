<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_targets', function (Blueprint $table) {
            $table->unique(
                ['financial_year_id', 'indicator_id', 'target_name'],
                'sub_targets_year_indicator_name_unique' // Custom short name
            );
        });
    }

    public function down(): void
    {
        Schema::table('submission_targets', function (Blueprint $table) {
            $table->dropUnique('sub_targets_year_indicator_name_unique');
        });
    }
};
