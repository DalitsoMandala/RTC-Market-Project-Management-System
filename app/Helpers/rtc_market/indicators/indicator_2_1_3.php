<?php
namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use App\Traits\IndicatorClassTrait;

class indicator_2_1_3
{
    use FilterableQuery;
    use IndicatorClassTrait;

    public function getDisaggregations()
    {
        return [
            'Total'        => 0,
            'Cassava'      => 0,
            'Potato'       => 0,
            'Sweet potato' => 0,
        ];
    }
}
