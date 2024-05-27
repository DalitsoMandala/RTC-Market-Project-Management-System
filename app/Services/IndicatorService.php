<?php

namespace App\Services;

class IndicatorService
{
    protected $indicatorMap = [
        'A1' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-a1',
        ],
        // Add more indicators here
    ];

    public function getComponent($indicatorNo, $projectName)
    {

        return $this->indicatorMap[$indicatorNo][$projectName] ?? null;
    }
}
