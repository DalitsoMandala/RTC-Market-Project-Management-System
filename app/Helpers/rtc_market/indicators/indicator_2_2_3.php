<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use App\Models\Indicator;
use App\Models\Recruitment;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class indicator_2_2_3
{
    use FilterableQuery;

    protected $financial_year;
    protected $reporting_period;
    protected $organisation_id;
    protected $enterprise;

    protected $crops = [
        'Cassava' => 'cassava',
        'Potato' => 'potato',
        'Sweet potato' => 'sweet_potato'
    ];

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Builders
    |--------------------------------------------------------------------------
    */

    public function builderFarmer($crop = null): Builder
    {
        $query = RtcProductionFarmer::query()
            ->where('status', 'approved')
            ->where('is_registered_seed_producer', true);



        return $this->applyFilters($query);
    }

    public function builderRecruitment($crop = null): Builder
    {
        $query = Recruitment::query()->where('status', 'approved');


        return $this->applyFilters($query);
    }

    /*
    |--------------------------------------------------------------------------
    | Category Counts
    |--------------------------------------------------------------------------
    */

    public function getCategoryPos($crop = null)
    {
        return $this->builderFarmer($crop)
            ->where('group', 'Producer organization (PO)')
            ->count();
    }

    public function getCategoryNotIndividualFarmers($crop = null)
    {
        return $this->builderFarmer($crop)
            ->where('group', 'Other')
            ->count();
    }

    public function getCategorySeedMultipliers($crop = null)
    {
        return $this->builderFarmer($crop)
            ->where('category', 'Seed multiplier')
            ->count();
    }

    public function getCategoryLargeScaleFarmers($crop = null)
    {
        return $this->builderFarmer($crop)
            ->where('group', 'Large scale farm')
            ->count();
    }

    public function getCategorySME($crop = null)
    {
        return $this->builderFarmer($crop)
            ->where('group', 'Small medium enterprise (SME)')
            ->count();
    }

   /*
    |--------------------------------------------------------------------------
    | Crop Aggregation
    |--------------------------------------------------------------------------
    */

    public function getCrop()
    {
        $farmers = $this->builderFarmer()
            ->whereIn('enterprise', array_keys($this->crops))
            ->get()
            ->groupBy('enterprise');

        $result = [];

        foreach ($this->crops as $name => $key) {
            $enterpriseFarmers = $farmers->get($name, collect());

            $result[$key] = [
                // Assuming seed counts are relationships or attributes on the model
                'basic_seed'     => $enterpriseFarmers->where('category', 'Early generation seed producer')->count(),
                'certified_seed' => $enterpriseFarmers->where('category', 'Seed multiplier')->count(),
                "{$key}_count"   => $enterpriseFarmers->count()
            ];
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Calculations
    |--------------------------------------------------------------------------
    */

    public function calculatePercentage()
    {
        $registeredCount = $this->builderFarmer()->count();
        $totalPotentialCount = $this->builderRecruitment()->count();

        if ($totalPotentialCount === 0) {
            return 0;
        }

        return ($registeredCount / $totalPotentialCount) * 100;
    }

    /*
    |--------------------------------------------------------------------------
    | Final Disaggregation
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        $cropData = $this->getCrop();

        return [
            'Total (% Percentage)'          => 0,
            'Cassava'                       => $cropData['cassava']['cassava_count'] ?? 0,
            'Potato'                        => $cropData['potato']['potato_count'] ?? 0,
            'Sweet potato'                  => $cropData['sweet_potato']['sweet_potato_count'] ?? 0,
            'Basic'                         => $this->builderFarmer()->where('category', 'Early generation seed producer')->count(),
            'Certified'                     => $this->getCategorySeedMultipliers(),
            'POs'                           => $this->getCategoryPos(),
            'Individual farmers not in POs' => $this->getCategoryNotIndividualFarmers(),
            'Registered'                    => $this->builderFarmer()->count(),
            'Seed multipliers'              => $this->getCategorySeedMultipliers(),
            'Large scale'                   => $this->builderFarmer()->where('group', 'Large scale farm')->count(),
            'Small scale'                   => $this->builderFarmer()->where('group', 'Small medium enterprise (SME)')->count(),
        ];
    }
}
