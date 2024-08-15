<?php

namespace Database\Seeders;

use App\Models\SystemDetail;
use App\Models\System_Detail;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        SystemDetail::create([
            'name' => 'CIP DATABASE MANAGEMENT SYSTEM',
            'address' => 'International Potato Center, Area 11 Plot 36, Chimutu Road, P.O Box 31600, Capital City, Lilongwe, Malawi',
            'website' => 'https://cipotato.org',
            'phone' => '265-1-123-456', // Example phone number
            'email' => 'info@cipotato.org', // Example email address
            'maintenance_mode' => false, // Initial value for maintenance mode
            'maintenance_message' => null, // Initial value for maintenance message
        ]);

    }
}
