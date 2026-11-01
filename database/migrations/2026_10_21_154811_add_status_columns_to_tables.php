<?php

use App\Models\Cgiar_Project;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up(): void
    {
    if (! Schema::hasColumn('cgiar_projects', 'is_active')) {
        Schema::table('cgiar_projects', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('slug');
        });
    }

    // Fix the projects table - change is_active type
    Schema::table('projects', function (Blueprint $table) {
        $table->boolean('is_active')->default(false)->change();
    });

    Project::where('name', 'RTC MARKET')->update(['is_active' => true]);
    Cgiar_Project::where('name', 'International Potato Center')->update(['is_active' => true]);
}};