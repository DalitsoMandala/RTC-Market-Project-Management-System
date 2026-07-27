<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_report_data', function (Blueprint $table) {
            $table->unique(
                ['system_report_id', 'name'],
                'system_report_data_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('system_report_data', function (Blueprint $table) {
            $table->dropUnique('system_report_data_unique');
        });
    }
};
