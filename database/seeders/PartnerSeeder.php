<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $partners = ['CIP', 'IITA', 'DAES', 'DCD', 'MINISTRY OF TRADE', 'TRADELINE', 'DARS', 'RTCDT', 'ACE'];

        foreach ($partners as $partner) {

            $partner = Partner::create(['name' => $partner, 'project_id' => 1]); // for RTC MARKET
        }

    }
}
