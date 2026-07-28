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
        Schema::table('indicators', function (Blueprint $table) {
            //
            $table->string('previous_indicator_no')->after('indicator_no')->nullable();
            $table->string('previous_indicator_name')->after('indicator_name')->nullable();
            $table->integer('previous_indicator_id')->after('id')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            //
            $table->dropColumn('previous_indicator_no');
            $table->dropColumn('previous_indicator_name');
            $table->dropColumn('previous_indicator_id');
            $table->dropColumn('is_active');
        });
    }
};
