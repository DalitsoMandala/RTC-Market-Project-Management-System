<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_4_1_5 extends base
{
    public function builder()
    {
        $indicatorId = Indicator::where('indicator_no', '4.1.5')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }

    public function getDisaggregations()
    {
        return $this->getTotalReport($this->builder(), self::pullTotals('4.1.5'))->toArray();
    }
}
