<?php

namespace App\Helpers;

use App\Helpers\DistrictObject;
use App\Models\AttendanceRegister;
use App\Models\FarmerSeedRegistration;
use App\Models\FinancialYear;
use App\Models\HouseholdRtcConsumption;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Recruitment;
use App\Models\RecruitSeedRegistration;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\SchoolRtcConsumption;
use App\Models\SeedBeneficiary;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\SubmissionTarget;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestData
{

public function __construct()
{
    $newA1Dissagregations = [
        'Transporters'
    ];
    $this->updateDis('A1', $newA1Dissagregations);
}
    public function updateDis($indicator_name, array $disaggregationData)
    {
        $indicator = Indicator::where('name', $indicator_name)->first();
        if ($indicator) {
            foreach ($disaggregationData as $disagg) {
                IndicatorDisaggregation::updateOrCreate(
                    [
                        'name' => $disagg,
                        ],

                    [
                        'name' => $disagg,
                        'indicator_id' => $indicator->id,
                    ]
                );
            }
        }
    }
}
