<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gross_margin_details', function (Blueprint $table) {
            //
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('gross_id')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gross_margin_details', function (Blueprint $table) {
            //

            $table->dropForeign(['user_id']);
            $table->dropForeign(['organisation_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('organisation_id');
            $table->dropColumn('gross_id');
        });
    }
};
