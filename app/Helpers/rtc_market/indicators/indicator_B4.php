<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_B4 extends base
{
    public function builder()
    {
        $indicatorId = Indicator::where('indicator_no', 'B4')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }

    public function getDisaggregations()
    {
        return $this->getTotalReport($this->builder(), self::pullTotals('B4'))->toArray();
    }

}
