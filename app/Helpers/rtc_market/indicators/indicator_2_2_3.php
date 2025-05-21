<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use App\Models\Recruitment;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_3
{
    use FilterableQuery;
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }
    public function builderFarmer($crop = null): Builder
    {


        $query = RtcProductionFarmer::query()->where('status', 'approved')->where('is_registered_seed_producer', true);

        return $this->applyFilters($query);
    }


    public function builderRecruitment($crop = null): Builder
    {


        $query = Recruitment::query()->where('status', 'approved');



        return $this->applyFilters($query);
    }

    public function getCategoryPos($crop = null)
    {

        // Use builderFarmer with specified crop and filter by type
        return $this->builderRecruitment($crop)

            ->where('group', 'Producer organization (PO)')
            ->count();
    }

    public function getCategoryIndividualFarmers($crop = null)
    {
        // Use builderFarmer with specified crop and filter by type
        return $this->builderRecruitment($crop)

            ->where('group', 'Large scale farm')
            ->count();
    }



    public function getCrop()
    {
        $farmers = $this->builderFarmer()
            ->with(['basicSeed', 'certifiedSeed'])

            ->get();

        // Group farmers by crop type
        $grouped = $farmers->groupBy('enterprise');

        return [
            'cassava' => [
                'basic_seed' => $grouped->get('Cassava')?->sum(fn($farmer) => $farmer->basicSeed->count()) ?? 0,
                'certified_seed' => $grouped->get('Cassava')?->sum(fn($farmer) => $farmer->certifiedSeed->count()) ?? 0,
                'cassava_count' => $grouped->get('Cassava')?->count() ?? 0,
            ],
            'potato' => [
                'basic_seed' => $grouped->get('Potato')?->sum(fn($farmer) => $farmer->basicSeed->count()) ?? 0,
                'certified_seed' => $grouped->get('Potato')?->sum(fn($farmer) => $farmer->certifiedSeed->count()) ?? 0,
                'potato_count' => $grouped->get('Potato')?->count() ?? 0,
            ],
            'sweet_potato' => [
                'basic_seed' => $grouped->get('Sweet potato')?->sum(fn($farmer) => $farmer->basicSeed->count()) ?? 0,
                'certified_seed' => $grouped->get('Sweet potato')?->sum(fn($farmer) => $farmer->certifiedSeed->count()) ?? 0,
                'sweet_potato_count' => $grouped->get('Sweet potato')?->count() ?? 0,
            ],
        ];
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage seed multipliers with formal registration')->where('indicator_no', '2.2.3')->first();
        if (!$indicator) {
            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }

    public function getDisaggregations()
    {
        $cropData = $this->getCrop(); // Store crop data to avoid redundant calls

        return [
            'Total (% Percentage)' => 0,
            'Cassava' => $cropData['cassava']['cassava_count'],
            'Potato' => $cropData['potato']['potato_count'],
            'Sweet potato' => $cropData['sweet_potato']['sweet_potato_count'],
            'Basic' => $cropData['cassava']['basic_seed'] + $cropData['potato']['basic_seed'] + $cropData['sweet_potato']['basic_seed'],
            'Certified' => $cropData['cassava']['certified_seed'] + $cropData['potato']['certified_seed'] + $cropData['sweet_potato']['certified_seed'],
            'POs' => $this->getCategoryPos(),
            'Individual farmers not in POs' => $this->getCategoryIndividualFarmers(),
        ];
    }
}
