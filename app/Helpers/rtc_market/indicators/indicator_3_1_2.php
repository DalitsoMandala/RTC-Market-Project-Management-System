<?php
namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use App\Traits\IndicatorClassTrait;

class indicator_3_1_2
{
    use IndicatorClassTrait;
    use FilterableQuery;
    public function getDisaggregations(): array
    {
        return [
            'Total'                           => 0,
            'Farmers'                         => 0,
            'Processors'                      => 0,
            'Traders'                         => 0,
            'PO\'s'                           => 0,
            'Individual farmers not in PO\'s' => 0,
            'Large scale farmers'             => 0,
            'SMEs'                            => 0,
            'Loan'                            => 0,
            'Input financing'                 => 0,
        ];
    }

}
