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
        //
         Schema::table('percentage_increase_indicators', function (Blueprint $table) {
            //
           $table->foreignId('project_id')->nullable()->constrained('projects', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('crop',['Cassava', 'Potato', 'Sweet Potato'])->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('percentage_increase_indicators', function (Blueprint $table) {
            $table->dropForeign('percentage_increase_indicators_project_id_foreign');

            $table->dropColumn('project_id');
            $table->dropColumn('crop');
        });

    }
};
