<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Faker\Factory as Faker;
use App\Helpers\DistrictObject;
use Illuminate\Database\Seeder;
use App\Models\SubmissionReport;
use App\Models\RpmFarmerFollowUp;
use App\Models\AttendanceRegister;
use Illuminate\Support\Collection;
use App\Models\RtcProductionFarmer;
use App\Models\SchoolRtcConsumption;
use App\Models\RtcProductionProcessor;
use App\Models\HouseholdRtcConsumption;
use App\Models\IndicatorDisaggregation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerFollowUp;
use App\Helpers\TestData;
use App\Models\Recruitment;
use App\Models\SeedBeneficiary;

class DataGenerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        TestData::run();
    }
}
