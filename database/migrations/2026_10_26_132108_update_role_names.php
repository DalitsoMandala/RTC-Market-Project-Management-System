<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

        // 1. Find the role by its current slug name and update it
        $role = Role::where('name', 'project-manager')->first();

        if ($role) {
            $role->name = 'project-manager';
            $role->save();

            // 2. CRITICAL: Clear the Spatie cache so the application registers the new name immediately
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            echo "Role updated successfully!";
        } else {
            echo "Role 'project-manager' not found.";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Role::where('name', 'project-manager')->first()->update(['name' => 'project-manager']);
    }
};
