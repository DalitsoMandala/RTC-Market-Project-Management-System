<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

         Schema::table('financial_years', function (Blueprint $table) {
            //
            $table->string('status')
                ->default('inactive')
                ->after('end_date');  // Optional: specify column position

        });

  Role::create(['name' => 'enumerator']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //

            Schema::table('financial_years', function (Blueprint $table) {
$table->dropColumn('status');

            });

    }
};
