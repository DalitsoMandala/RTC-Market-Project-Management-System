<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Support\Facades\DB;

class A1
{

    public function cropCount($actor_type = null)
    {
        $db = HouseholdRtcConsumption::select([
            DB::raw('SUM(rtc_consumers_cassava) as cassava_count'),
            DB::raw('SUM(rtc_consumers_potato) as potato_count'),
            DB::raw('SUM(rtc_consumers_sw_potato) as sw_potato_count'),

        ]);

        if ($actor_type != null) {

            $db->where('actor_type', $actor_type);

        }

        $result = $db->first();

// Ensure the result is not null
        if ($result) {
            return $result->toArray();
        }

// Return an empty array or some default values if the result is null
        return [
            'cassava_count' => 0,
            'potato_count' => 0,
            'sw_potato_count' => 0,
        ];

    }

}
