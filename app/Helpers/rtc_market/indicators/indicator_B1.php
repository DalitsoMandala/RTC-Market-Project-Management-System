<?php
namespace App\Helpers\rtc_market\indicators;

use App\Helpers\rtc_market\indicators\base;
use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_B1 extends base
{

    public function builder()
    {
        $indicatorId = Indicator::where('indicator_no', 'B1')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }

    public function getDisaggregations()
    {
        return $this->getTotalReport($this->builder(), self::pullTotals('B1'))->toArray();
    }
}
