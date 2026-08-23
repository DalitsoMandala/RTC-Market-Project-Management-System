<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\SeedBeneficiary;
use App\Traits\InterventionsTrait;
use Illuminate\Database\Eloquent\Builder;

class indicator_B3 extends base
{
    use InterventionsTrait;

    public function builder(): Builder
    {
        return $this->applySeedFilters(
            SeedBeneficiary::query()->where('status', 'approved')
        );
    }

    public function getDisaggregations()
    {
        $disaggregations = $this->getIndicatorDisaggregations('B3');

        $Total = $this->indicator345Data()['Total'] ?? 0 + $this->indicator346Data()['Total'] ?? 0 + $this->indicator347Data()['caregroups'] ?? 0 + $this->nutritionTrainingsData()['nutrition_trainings'] ?? 0;
        $disaggregations->put('Total', $Total ?? 0);
        $disaggregations->put('Households', $this->householdsData()['household_size'] ?? 0);
        $disaggregations->put('Caregroups', $this->indicator347Data()['caregroups'] ?? 0);
        $disaggregations->put('Mass campaigns', $this->indicator345Data()['Total'] ?? 0);
        return $disaggregations->toArray();
    }
}
