<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\Http\Request;
use App\Models\IndicatorDisaggregation;

class AddDisaggregationController extends Controller
{
    //
    public function add(){
    $merge = [];
        $newDisaggregations = ['Aggregators','Transporters'];

        $indicatorA1 = Indicator::where('indicator_no', 'A1')->first();

        foreach($newDisaggregations as $disagg){

            IndicatorDisaggregation::firstOrCreate(['indicator_id' => $indicatorA1->id, 'name' => $disagg]);

        }

    }
}
