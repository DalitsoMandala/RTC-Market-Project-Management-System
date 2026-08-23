<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;

class indicator_3_3_4 extends base
{
    public function builder()
    {
        $indicatorId = Indicator::where('indicator_no', '3.3.4')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }

    public function getDisaggregations()
    {
        return $this->getTotalReport($this->builder(), self::pullTotals('3.3.4'))->toArray();
    }
}
