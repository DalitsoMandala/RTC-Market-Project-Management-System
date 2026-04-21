<?php

use App\Models\Crop;
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

        Schema::table('crops', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
        });

        Crop::whereIn('name', ['Cassava', 'Sweet potato', 'Potato'])->update(['is_default' => true]);
        $cassavaVarieties = ['Chinangwa 3', 'Chinangwa 2', 'Chinangwa 1', 'Mpale', 'Kalawe', 'Chamandanda', 'Sagonja', 'Chiombola', 'Mulola', 'Phoso', 'Sauti', 'Yizaso', 'Silira', 'Maunjili', 'Mkondezi', 'Mbundumali', 'Manyokola'];
        foreach ($cassavaVarieties as $cassavaVariety) {
            Crop::where('name', 'Cassava')->first()->varieties()->firstOrCreate(['name' => strtolower($cassavaVariety)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
