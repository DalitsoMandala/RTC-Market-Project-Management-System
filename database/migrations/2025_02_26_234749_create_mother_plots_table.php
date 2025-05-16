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
        Schema::create('mother_plots', function (Blueprint $table) {
            $table->id();
            $table->string('district'); // District
            $table->string('epa'); // EPA
            $table->string('section'); // Section
            $table->string('village'); // Village
            $table->string('gps_s'); // GPS (S)
            $table->string('gps_e'); // GPS (E)
            $table->decimal('elevation', 18, 2); // Elevation (m)
            $table->tinyInteger('season')->comment('1=Rainfed, 2=Winter'); // Season
            $table->date('date_of_planting'); // Date of Planting
            $table->string('name_of_farmer'); // Name of Farmer
            $table->tinyInteger('sex')->comment('1=Male, 2=Female'); // Sex
            $table->string('nat_id_phone_number'); // Nat ID / Phone #
            $table->string('variety_received')
                ->comment('1=Royal Choice, 2=Kaphulira, 3=Chipika, 4=Mathuthu, 5=Kadyaubwelere, 6=Sungani, 7=Kajiyani, 8=Mugamba, 9=Kenya, 10=Nyamoyo, 11=Anaakwanire, 12=Other'); // Variety received (as string)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mother_plots');
    }
};
