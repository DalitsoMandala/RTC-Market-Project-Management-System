<?php

namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Seeder;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $organisations = ['CIP', 'DESIRA', 'IITA', 'DAES', 'DCD', 'MINISTRY OF TRADE', 'TRADELINE', 'DARS', 'RTCDT', 'ACE'];

        foreach ($organisations as $partner) {

            $partner = Organisation::create(['name' => $partner]); // for RTC MARKET
        }
    }
}
