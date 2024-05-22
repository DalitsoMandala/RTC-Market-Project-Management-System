<?php

namespace Database\Seeders;

use App\Models\System_Detail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        System_Detail::create([
            'name' => 'CIP DATABASE MANAGEMENT SYSTEM',
            'address' => 'International Potato Center, Area 11 Plot 36, Chimutu Road, P.O BoX 31600,Capital City, Lilongwe, Malawi',
            'website' => e('https://cipotato.org')
        ]);
    }
}