<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\SeedBeneficiary;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_4_7 extends base
{
    public function builder(): Builder
    {
        return $this->applySeedFilters(
            SeedBeneficiary::query()->where('status', 'approved')
        );
    }

    public function getDisaggregations()
    {
        $stats = $this->builder()

            ->selectRaw("
        SUM(household_size - child_under_school_fd) as Total
            ")
            ->first();

        $disaggregations = $this->getIndicatorDisaggregations('3.4.7');

        $disaggregations->put('Total', (int) ($stats->total ?? 0));

        return $disaggregations->toArray();
    }
}
