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


    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
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



    public function builderFarmer($crop = null): Builder
    {
        $query = RtcProductionFarmer::query()
            ->where('status', 'approved')
            ->where('is_registered_seed_producer', true);

        // Apply enterprise filter if set in constructor
        if ($this->enterprise) {
            $query->where('enterprise', $this->enterprise);
        } elseif ($crop) {
            $query->where('enterprise', $crop);
        }

        return $this->applyFilters($query);
    }



    public function builderRecruitment($crop = null): Builder
    {
        $query = Recruitment::query()->where('status', 'approved');

        // Apply enterprise filter if set in constructor
        if ($this->enterprise) {
            $query->where('enterprise', $this->enterprise);
        } elseif ($crop) {
            $query->where('enterprise', $crop);
        }

        return $this->applyFilters($query);
    }

    public function getCrop()
    {
        $farmers = $this->builderFarmer()
            ->with(['basicSeed', 'certifiedSeed'])
            ->get();

        // If enterprise is set, return only that enterprise's data
        if ($this->enterprise) {
            $enterpriseKey = strtolower(str_replace(' ', '_', $this->enterprise));
            $grouped = $farmers->groupBy('enterprise');

            return [
                $enterpriseKey => [
                    'basic_seed' => $grouped->first()?->sum(fn($farmer) => $farmer->basicSeed->count()) ?? 0,
                    'certified_seed' => $grouped->first()?->sum(fn($farmer) => $farmer->certifiedSeed->count()) ?? 0,
                    $enterpriseKey . '_count' => $farmers->count(),
                ]
            ];
        }

        // Otherwise return all crops
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
        $cropData = $this->getCrop();

        // Initialize all crop counts (will be 0 if enterprise is filtered)
        $cassavaCount = $cropData['cassava']['cassava_count'] ?? 0;
        $potatoCount = $cropData['potato']['potato_count'] ?? 0;
        $sweetPotatoCount = $cropData['sweet_potato']['sweet_potato_count'] ?? 0;

        // Calculate totals
        $totalFarmers = $cassavaCount + $potatoCount + $sweetPotatoCount;
        $totalBasic = ($cropData['cassava']['basic_seed'] ?? 0)
            + ($cropData['potato']['basic_seed'] ?? 0)
            + ($cropData['sweet_potato']['basic_seed'] ?? 0);
        $totalCertified = ($cropData['cassava']['certified_seed'] ?? 0)
            + ($cropData['potato']['certified_seed'] ?? 0)
            + ($cropData['sweet_potato']['certified_seed'] ?? 0);


        // Prepare base response structure
        $result = [
            'Total (% Percentage)' => 0,
            'Cassava' => $cassavaCount,
            'Potato' => $potatoCount,
            'Sweet potato' => $sweetPotatoCount,
            'Basic' => $totalBasic,
            'Certified' => $totalCertified,
            'POs' => 0,
            'Individual farmers not in POs' => 0,
            'Registered' => $this->builderFarmer()->count(),
            'Seed multipliers' => 0,
            'Large scale' => 0,
            'Small scale' => 0
        ];

        // If enterprise is filtered, keep all keys but zero out non-matching crops
        if ($this->enterprise) {
            $enterpriseKey = strtolower(str_replace(' ', '_', $this->enterprise));

            foreach (['Cassava', 'Potato', 'Sweet potato'] as $crop) {
                $key = str_replace(' ', '_', strtolower($crop));
                if ($key !== $enterpriseKey) {
                    $result[$crop] = 0;
                }
            }
        }

        return $result;
    }
}
