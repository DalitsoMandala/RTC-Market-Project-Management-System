<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class A1
{

    public static function cropCount(): Builder
    {
        return HouseholdRtcConsumption::query()->select([
            DB::raw('SUM(rtc_consumers_cassava) as cassava_count'),
            DB::raw('SUM(rtc_consumers_potato) as potato_count'),
            DB::raw('SUM(rtc_consumers_sw_potato) as sw_potato_count'),
        ]);
    }

    public static function overallCropCount(): array
    {
        $result = self::cropCount()->first();

        if ($result) {
            return $result->toArray();
        }

        return [
            'cassava_count' => 0,
            'potato_count' => 0,
            'sw_potato_count' => 0,

        ];
    }
    public function cropCountByRespondent(?string $actor_type): array
    {
        $db = self::cropCount();

        if ($actor_type) {
            $db->where('actor_type', $actor_type);
            // dd($db->first());
        }

        $result = $db->first();

        if ($result) {
            return $result->toArray();
        }

        return [
            'cassava_count' => 0,
            'potato_count' => 0,
            'sw_potato_count' => 0,
        ];
    }

    public function cropsPercentage($cropArray)
    {
        $cassava = $cropArray['cassava  _count'] ?? 0;
        $potato = $cropArray['potato_count'] ?? 0;
        $sw_potato = $cropArray['sw_potato_count'] ?? 0;
        $total = $cassava + $potato + $sw_potato;
        if ($total === 0) {
            return [
                'cassava_count' => 0,
                'potato_count' => 0,
                'sw_potato_count' => 0,
            ];
        } else {

            return [
                'cassava_count' => round(($cassava / $total) * 100, 2),
                'potato_count' => round(($potato / $total) * 100, 2),
                'sw_potato_count' => round(($sw_potato / $total) * 100, 2),

            ];
        }

    }
}
