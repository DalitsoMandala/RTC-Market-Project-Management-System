<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\AttendanceRegister;
use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_2_2
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
    // public function builder(): Builder
    // {

    //     $indicator = Indicator::where('indicator_name', 'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)')->where('indicator_no', '3.2.2')->first();

    //     $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

    //     // Check if both reporting period and financial year are set
    //     if ($this->reporting_period || $this->financial_year) {
    //         // Apply filter for reporting period if it's set
    //         if ($this->reporting_period) {
    //             $query->where('period_month_id', $this->reporting_period);
    //         }

    //         // Apply filter for financial year if it's set
    //         if ($this->financial_year) {
    //             $query->where('financial_year_id', $this->financial_year);
    //         }

    //         // If no data is found, return an empty result
    //         if (!$query->exists()) {
    //             $query->whereIn('id', []); // Empty result filter
    //         }
    //     }

    //     // Filter by organization if set
    //     if ($this->organisation_id) {
    //         $query->where('organisation_id', $this->organisation_id);
    //     }




    //     return $query;
    // }

    public function builder(): Builder
    {

        $query = AttendanceRegister::query()->where('status', 'approved');






        return $this->applyAttendanceFilters($query);
    }

    public function getCropType()
    {


        if ($this->enterprise) {

            $query = $this->builder()->count();
            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $query,
            ];
        }
        $cassava = $this->builder()->where('rtcCrop_cassava', true)->count();
        $potato = $this->builder()->where('rtcCrop_potato', true)->count();
        $sweetPotato = $this->builder()->where('rtcCrop_sweet_potato', true)->count();
        return [
            'cassava' => $cassava,
            'potato' => $potato,
            'sweet_potato' => $sweetPotato
        ];
    }

    public function getCategory()
    {
        $farmers = $this->builder()->where('category', 'Farmer')->count();
        $processors = $this->builder()->where('category', 'Processor')->count();

        $traders = $this->builder()->where('category', 'Trader')->count();
        $partners = $this->builder()->where('category', 'Partner')->count();
        $staff = $this->builder()->where('category', 'Staff')->count();


        return [
            'Farmers' => $farmers,
            'Processors' => $processors,
            'Traders' => $traders,
            'Partners' => $partners,
            'Staff' => $staff
        ];
    }


    public function getDisaggregations()
    {



        $crop = $this->getCropType();

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
        $farmers = $this->getCategory()['Farmers'];
        $processors = $this->getCategory()['Processors'];
        $traders = $this->getCategory()['Traders'];
        $partners = $this->getCategory()['Partners'];
        $staff = $this->getCategory()['Staff'];

        return [
            'Total' => $staff + $farmers + $processors + $traders + $partners,
            ...$allCrops,
            'Farmers' => $farmers,
            'Processors' => $processors,
            'Traders' => $traders,
            'Partner' => $partners,
            'Staff' => $staff,
        ];
    }
}