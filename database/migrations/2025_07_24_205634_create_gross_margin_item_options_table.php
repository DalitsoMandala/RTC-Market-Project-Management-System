<?php

use App\Models\GrossMarginItemOption;
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
        Schema::create('gross_margin_item_options', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $data = [
            'Rent',
            'Land clearing',
            'Ridging',
            'Seed',
            'Basal fertilizer',
            'Top dressing fertilizer',
            'Planting',
            'Weeding',
            'Banking',
            'Spraying',
            'Hiring knapsack sprayers',
            'Fungicides',
            'Pesticides',
            'Harvesting',
            'Sacks',
            'Transport',
        ];

        foreach ($data as $item) {
            GrossMarginItemOption::create([
                'name' => $item,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_margin_item_options');
    }
};
