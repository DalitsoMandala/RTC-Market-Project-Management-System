<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_2
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
    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query()
            ->where('status', 'approved')
            ->with([
                'basicSeed',
                'certifiedSeed'
            ]);



        return $this->applyFilters($query);
    }

    public function getBasicSeed($crop = null)
    {
        $totalArea = 0;
        $query = $this->builderFarmer();

        // If enterprise is set in constructor, it takes priority over $crop parameter
        $filterCrop = $this->enterprise ? $this->enterprise : $crop;

        if ($filterCrop) {
            $query->where('enterprise', $filterCrop);
        }

        $query->chunk(1000, function ($farmers) use (&$totalArea) {
            $basicSeedArea = $farmers->pluck('basicSeed')->flatten()->sum('area');
            $totalArea += $basicSeedArea;
        });

        return $totalArea;
    }

    public function getCertifiedSeed($crop = null)
    {
        $totalArea = 0;
        $query = $this->builderFarmer();

        // If enterprise is set in constructor, it takes priority over $crop parameter
        $filterCrop = $this->enterprise ? $this->enterprise : $crop;

        if ($filterCrop) {
            $query->where('enterprise', $filterCrop);
        }

        $query->chunk(1000, function ($farmers) use (&$totalArea) {
            $certifiedSeedArea = $farmers->pluck('certifiedSeed')->flatten()->sum('area');
            $totalArea += $certifiedSeedArea;
        });

        return $totalArea;
    }

    public function getCrop()
    {
        // If enterprise is set, only calculate for that enterprise
        if ($this->enterprise) {
            $basic = $this->getBasicSeed();
            $certified = $this->getCertifiedSeed();

            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $basic + $certified
            ];
        }

        // Otherwise calculate for all crops
        return [
            'cassava' => $this->getBasicSeed('Cassava') + $this->getCertifiedSeed('Cassava'),
            'potato' => $this->getBasicSeed('Potato') + $this->getCertifiedSeed('Potato'),
            'sweet_potato' => $this->getBasicSeed('Sweet potato') + $this->getCertifiedSeed('Sweet potato'),
        ];
    }



    public function getDisaggregations()
    {
        $crop = $this->getCrop();

        // Define all possible crops with default 0 values
        $allCrops = [
            'Cassava' => 0,
            'Sweet potato' => 0,
            'Potato' => 0,
        ];

        // Merge actual values (if they exist)
        foreach ($allCrops as $key => $value) {
            $snakeKey = strtolower(str_replace(' ', '_', $key));
            if (isset($crop[$snakeKey])) {
                $allCrops[$key] = round($crop[$snakeKey], 2);
            }
        }

        $total = $this->getBasicSeed() + $this->getCertifiedSeed();

        return [
            'Total' => $total,
            ...$allCrops,
            'Basic' => $this->getBasicSeed(),
            'Certified' => $this->getCertifiedSeed(),
        ];
    }
}
