<?php
namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use App\Traits\IndicatorClassTrait;

class indicator_2_1_5
{
    // Automatically generated for indicator 2.1.5
    use FilterableQuery;
    use IndicatorClassTrait;

    public function getDisaggregations()
    {
        return [
            'Total'                           => 0,
            'Domestic'                        => 0,
            'International'                   => 0,
            'Cassava'                         => 0,
            'Potato'                          => 0,
            'Sweet potato'                    => 0,
            'PO\'s'                           => 0,
            'Individual farmers not in PO\'s' => 0,
            'Large scale commercial farmers'  => 0,
        ];
    }
}
