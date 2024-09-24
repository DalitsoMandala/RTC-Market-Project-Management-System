<?php

namespace App\Helpers\rtc_market\indicators;


use App\Livewire\Internal\Cip\Submissions;
use App\Models\HouseholdRtcConsumption;
use App\Models\Organisation;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class indicator_A1
{

    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;



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
    public function builder(): Builder
    {
        $query = HouseholdRtcConsumption::query()->where('status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                $query = $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                $query = $query->whereIn('id', []);
            }
        }

        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);

        }

        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }


        return $query;
    }

    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query()->with('followups')->where('status', 'approved');

        // Check if both reporting period and financial year are set
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
            }
        }

        // Filter by organization if set
        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);
        }


        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }

        return $query;
    }


    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->with('followups')->where('status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $hasValidBatchUuids = false;

            $query->where(function ($query) use (&$hasValidBatchUuids) {

                $submissionPeriod = SubmissionPeriod::where('month_range_period_id', $this->reporting_period)->where('financial_year_id', $this->financial_year)->pluck('id')->toArray();
                if (!empty($submissionPeriod)) {
                    $batchUuids = Submission::whereIn('period_id', $submissionPeriod)->pluck('batch_no')->toArray();
                    if (!empty($batchUuids)) {
                        $query->orWhereIn('uuid', $batchUuids);
                        $hasValidBatchUuids = true;
                    }
                }



            });

            if (!$hasValidBatchUuids) {
                // No valid batch UUIDs found, return an empty collection
                return $query->whereIn('uuid', []);
            }
        }

        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }

        return $query;
    }


    public function findTotalFarmerEmployees()
    {
        // Retrieve all farmer records
        $data = $this->builderFarmer()->get();

        // Map over the data and calculate totals for each record
        $employees = $data->map(function ($model) {

            // Calculate the formal employment total
            $model->empFormalTotal = $model->emp_formal_female_18_35
                + $model->emp_formal_male_18_35
                + $model->emp_formal_male_35_plus
                + $model->emp_formal_female_35_plus;

            // Calculate the informal employment total
            $model->empInFormalTotal = $model->emp_informal_female_18_35
                + $model->emp_informal_male_18_35
                + $model->emp_informal_male_35_plus
                + $model->emp_informal_female_35_plus;

            return $model;
        });

        // Sum the totals for all records
        $totalEmpFormal = $employees->sum('empFormalTotal');
        $totalEmpInFormal = $employees->sum('empInFormalTotal');

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];
    }


    public function findTotalProcessorEmployees()
    {
        $data = $this->builderProcessor()->get();
        // Map over the data and calculate totals for each record
        $employees = $data->map(function ($model) {

            // Calculate the formal employment total
            $model->empFormalTotal = $model->emp_formal_female_18_35
                + $model->emp_formal_male_18_35
                + $model->emp_formal_male_35_plus
                + $model->emp_formal_female_35_plus;

            // Calculate the informal employment total
            $model->empInFormalTotal = $model->emp_informal_female_18_35
                + $model->emp_informal_male_18_35
                + $model->emp_informal_male_35_plus
                + $model->emp_informal_female_35_plus;

            return $model;
        });

        // Sum the totals for all records
        $totalEmpFormal = $employees->sum('empFormalTotal');
        $totalEmpInFormal = $employees->sum('empInFormalTotal');

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];

    }

    public function findTotal()
    {

        $totalFarmerEmployees = $this->findTotalFarmerEmployees();

        $totalProcessorEmployees = $this->findTotalProcessorEmployees();
        $totalEmployees = $totalFarmerEmployees['Total'] + $totalProcessorEmployees['Total'];
        return $this->builder()->count() + $totalEmployees;
    }

    public function findGender()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN sex = \'Male\' THEN 1 ELSE 0 END) AS MaleCount'),
                DB::raw('SUM(CASE WHEN sex = \'Female\' THEN 1 ELSE 0 END) AS FemaleCount'),
            ])

            ->first()->toArray();
    }
    public function findAge()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN age_group = \'Youth\' THEN 1 ELSE 0 END) AS youth'),
                DB::raw('SUM(CASE WHEN age_group = \'Not youth\' THEN 1 ELSE 0 END) AS not_youth'),
            ])

            ->first()->toArray();
    }

    public function findActorType()
    {
        return $this->countActor()->first()->toArray();
    }

    public function countCrop()
    {
        return $this->builder()
            ->select([
                DB::raw('SUM(rtc_consumers_potato) as potato'),
                DB::raw('SUM(rtc_consumers_cassava) as cassava'),
                DB::raw('SUM(rtc_consumers_sw_potato) as sweet_potato'),
            ])

        ;
    }

    public function countActor()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN actor_type = \'FARMER\' THEN 1 ELSE 0 END) AS farmer'),
                DB::raw('SUM(CASE WHEN actor_type = \'PROCESSOR\' THEN 1 ELSE 0 END) AS processor'),
                DB::raw('SUM(CASE WHEN actor_type = \'TRADER\' THEN 1 ELSE 0 END) AS trader'),
            ])

        ;
    }
    public function findByCrop()
    {
        return $this->countCrop()->first()->toArray();
    }

    public function RtcActorByCrop($actor)
    {
        return $this->countCrop()->where('actor_type', $actor)->first()->toArray();

    }

    public function RtcActorBySex($sex)
    {
        return $this->countActor()->where('sex', $sex)->first()->toArray();

    }
    public function RtcActorByAge($age)
    {
        return $this->countActor()->where('age_group', $age)->first()->toArray();

    }

    public function getEstablishmentFarmers()
    {
        return $this->builderFarmer()->select([
            DB::raw('COUNT(*) AS Total'),
            DB::raw('SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS NEW'),
            DB::raw('SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS OLD'),

        ])->first()->toArray();

    }

    public function getEstablishmentProcessors()
    {
        return $this->builderProcessor()->select([
            DB::raw('COUNT(*) AS Total'),
            DB::raw('SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS NEW'),
            DB::raw('SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS OLD'),

        ])->first()->toArray();
    }

    public function getCurrentTargets($financial_year_ids, $organisation_ids)
    {
        $builder = $this->builder()->select([
            'organisation_id',
            DB::raw('COUNT(*) AS Total'),
        ])
            ->where('actor_name', '!=', null)
            ->whereIn('organisation_id', $organisation_ids)
            ->whereIn('financial_year_id', $financial_year_ids)
            ->groupBy('organisation_id');

        $final = $builder->get()->map(function ($query) {
            $model = Organisation::find($query->organisation_id);
            $query->organisation = $model->name;
            return $query;
        });


        return $final->toArray();
    }

    public function getDisaggregations()
    {
        $gender = $this->findGender();
        $age = $this->findAge();
        $actorType = $this->findActorType();
        $crop = $this->findByCrop();


        $totalFarmerEmployees = $this->findTotalFarmerEmployees();

        $totalProcessorEmployees = $this->findTotalProcessorEmployees();
        $totalEmployees = $totalFarmerEmployees['Total'] + $totalProcessorEmployees['Total'];
        $totalOldEstablishment = $this->getEstablishmentFarmers()['OLD'] + $this->getEstablishmentProcessors()['OLD'];
        $totalNewEstablishment = $this->getEstablishmentFarmers()['NEW'] + $this->getEstablishmentProcessors()['NEW'];

        return [
            'Total' => $this->findTotal(),
            'Female' => (float) $gender['FemaleCount'],
            'Male' => (float) $gender['MaleCount'],
            'Youth (18-35 yrs)' => (float) $age['youth'],
            'Not youth (35yrs+)' => (float) $age['not_youth'],
            'Farmers' => (float) $actorType['farmer'],
            'Processors' => (float) $actorType['processor'],
            'Traders' => (float) $actorType['trader'],
            'Cassava' => (float) $crop['cassava'],
            'Potato' => (float) $crop['potato'],
            'Sweet potato' => (float) $crop['sweet_potato'],
            'Employees on RTC establishment' => $totalEmployees,
            'New establishment' => $totalNewEstablishment,
            'Old establishment' => $totalOldEstablishment,
        ];

    }
}
