<?php

namespace App\Helpers;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Builder;

class DisaggregationAppend
{
    public string $indicator_name;
    public array $data;

    public function __construct(string $indicator_name,$data = []){
        $this->indicator_name = $indicator_name;
        $this->data = $data;

    }
   public static function getIndicators(): Builder
    {
        return Indicator::query()->with('disaggregations');
    }

    public function findIndicator()
    {
        return self::getIndicators()
            ->where('indicator_name', $this->indicator_name)
            ->first();
    }

    public function appendDisaggregations()
    {
        $indicator = $this->findIndicator();
        $disaggregations = $indicator->disaggregations;
        return response()->json($disaggregations);
    }
}
