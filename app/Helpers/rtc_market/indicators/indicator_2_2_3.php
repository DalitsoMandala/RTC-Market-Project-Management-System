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

        $enterprise = $this->enterprise ?? $crop;

        if ($enterprise) {
            $query->where('enterprise', $enterprise);
        }

        return $this->applyFilters($query);
    }

    public function builderRecruitment($crop = null): Builder
    {
        $query = Recruitment::query()->where('status', 'approved');

        $enterprise = $this->enterprise ?? $crop;

        if ($enterprise) {
            $query->where('enterprise', $enterprise);
        }

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
            ->with(['basicSeed', 'certifiedSeed'])
            ->get()
            ->groupBy('enterprise');

        $result = [];

        foreach ($this->crops as $name => $key) {

            if ($this->enterprise && $this->enterprise !== $name) {
                $result[$key] = [
                    'basic_seed' => 0,
                    'certified_seed' => 0,
                    "{$key}_count" => 0
                ];
                continue;
            }

            $enterpriseFarmers = $farmers->get($name, collect());

            $result[$key] = [
                'basic_seed' => $enterpriseFarmers->sum(fn($f) => $f->basicSeed?->count() ?? 0),
                'certified_seed' => $enterpriseFarmers->sum(fn($f) => $f->certifiedSeed?->count() ?? 0),
                "{$key}_count" => $enterpriseFarmers->count()
            ];
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Indicator
    |--------------------------------------------------------------------------
    */

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_no', '2.2.3')
            ->where('indicator_name', 'Percentage seed multipliers with formal registration')
            ->first();

        if (!$indicator) {
            Log::error('Indicator 2.2.3 not found');
            return null;
        }

        return $indicator;
    }

    /*
    |--------------------------------------------------------------------------
    | Seed Types
    |--------------------------------------------------------------------------
    */

    public function getBasicSeed()
    {
        return $this->builderFarmer()
            ->where('category', 'Early generation seed producer')
            ->count();
    }

    public function getCertifiedSeed()
    {
        return $this->builderFarmer()
            ->where('category', 'Seed multiplier')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Final Disaggregation
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        $cropData = $this->getCrop();

        $cassava = $cropData['cassava']['cassava_count'] ?? 0;
        $potato = $cropData['potato']['potato_count'] ?? 0;
        $sweetPotato = $cropData['sweet_potato']['sweet_potato_count'] ?? 0;

        return [
            'Total (% Percentage)' => 0,
            'Cassava' => $cassava,
            'Potato' => $potato,
            'Sweet potato' => $sweetPotato,
            'Basic' => $this->getBasicSeed(),
            'Certified' => $this->getCertifiedSeed(),
            'POs' => $this->getCategoryPos(),
            'Individual farmers not in POs' => $this->getCategoryNotIndividualFarmers(),
            'Registered' => $this->builderFarmer()->count(),
            'Seed multipliers' => $this->getCategorySeedMultipliers(),
            'Large scale' => $this->getCategoryLargeScaleFarmers(),
            'Small scale' => $this->getCategorySME(),
        ];
    }
}
