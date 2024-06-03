<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::where('email', 'daes@example.com')->first();

        Partner::create([
            'organisation_name' => 'DAES',
            'user_id' => $user->id,
        ]);

        $user = User::where('email', 'dcd@example.com')->first();
        Partner::create([
            'organisation_name' => 'DCD',
            'user_id' => $user->id,
        ]);

        $user = User::where('email', 'iita@example.com')->first();
        Partner::create([
            'organisation_name' => 'IITA',
            'user_id' => $user->id,
        ]);

        $user = User::where('email', 'staff@example.com')->first();
        Partner::create([
            'organisation_name' => 'CIP',
            'user_id' => $user->id,
        ]);

        $user = User::where('email', 'organiserp@example.com')->first();
        Partner::create([
            'organisation_name' => 'CIP',
            'user_id' => $user->id,
        ]);

    }
}
