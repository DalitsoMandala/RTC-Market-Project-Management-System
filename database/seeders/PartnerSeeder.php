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
        //

        $user = User::find(5);
        Partner::create([
            'organisation_name' => 'IITA',
            'user_id' => $user->id,
        ]);

        Partner::create([
            'organisation_name' => 'DCD',
            'user_id' => $user->id,
        ]);

        Partner::create([
            'organisation_name' => 'DAES',
            'user_id' => $user->id,
        ]);

    }
}
