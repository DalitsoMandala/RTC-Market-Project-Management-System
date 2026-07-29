<?php
namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use App\Traits\IndicatorClassTrait;

class indicator_3_3_6
{
    use FilterableQuery;
    use IndicatorClassTrait;
    // Automatically generated for indicator 3.3.4
    public function getDisaggregations()
    {
        return [
            'Total'                           => 0,
            'Farmers'                         => 0,
            'Traders'                         => 0,
            'Transporters'                    => 0,
            'PO\'s'                           => 0,
            'Individual farmers not in PO\'s' => 0,
            'Large scale commercial farmers'  => 0,
            'Cassava'                         => 0,
            'Potato'                          => 0,
            'Sweet potato'                    => 0,
            'Registered'                      => 0,
            'Not registered'                  => 0,
        ];
    }
}
