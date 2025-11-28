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
        Schema::table('rtc_production_farmers', function (Blueprint $table) {
            //

            $table->string('group')->after('status')->nullable();
            $table->string('category')->after('status')->nullable();
            $table->string('sector')->after('status')->nullable();
        });

            Schema::table('rtc_production_processors', function (Blueprint $table) {
            //

            $table->string('group')->after('status')->nullable();
            $table->string('category')->after('status')->nullable();
                   $table->string('sector')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('rtc_production_farmers', function (Blueprint $table) {
            //
            $table->dropColumn('group');
            $table->dropColumn('category');
            $table->dropColumn('sector');
       });

       Schema::table('rtc_production_processors', function (Blueprint $table) {
            //
            $table->dropColumn('group');
            $table->dropColumn('category');
            $table->dropColumn('sector');
       });
    }
};
