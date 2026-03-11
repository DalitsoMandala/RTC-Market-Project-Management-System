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


    public function builder(): Builder
    {
        $query = AttendanceRegister::query()
            ->where('status', 'approved')
            ->where('category','!=','Other')
            ->select([
                'rtcCrop_cassava',
                'rtcCrop_potato',
                'rtcCrop_sweet_potato',
                'category',
                'id',
            ])
            ;



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
        $aggregators = $this->builder()->where('category', 'Aggregator')->count();
        $transporters = $this->builder()->where('category', 'Transporter')->count();


        return [
            'Farmers' => $farmers,
            'Processors' => $processors,
            'Traders' => $traders,
            'Partners' => $partners,
            'Staff' => $staff,
            'Aggregators' => $aggregators,
            'Transporters' => $transporters
        ];
    }


 public function getDisaggregations()
{
    $records = $this->builder()->get();

    // Unique individuals
    $uniquePeople = $records->unique('id');

    // Initialize crop counts
    $cassava = $potato = $sweetPotato = 0;

    if ($this->enterprise) {
        $enterpriseMap = [
            'Cassava' => 'rtcCrop_cassava',
            'Potato' => 'rtcCrop_potato',
            'Sweet potato' => 'rtcCrop_sweet_potato'
        ];

        if (isset($enterpriseMap[$this->enterprise])) {
            $cropField = $enterpriseMap[$this->enterprise];
            $count = $records->where($cropField, true)->unique('id')->count();

            // Set the selected crop, leave others 0
            switch ($this->enterprise) {
                case 'Cassava':
                    $cassava = $count;
                    break;
                case 'Potato':
                    $potato = $count;
                    break;
                case 'Sweet potato':
                    $sweetPotato = $count;
                    break;
            }
        }
    } else {
        // Count all crops normally
        $cassava = $records->where('rtcCrop_cassava', true)->unique('id')->count();
        $potato = $records->where('rtcCrop_potato', true)->unique('id')->count();
        $sweetPotato = $records->where('rtcCrop_sweet_potato', true)->unique('id')->count();
    }

    // Category counts (unique individuals per category)
    $farmers = $records->where('category', 'Farmer')->unique('id')->count();
    $processors = $records->where('category', 'Processor')->unique('id')->count();
    $traders = $records->where('category', 'Trader')->unique('id')->count();
    $partners = $records->where('category', 'Partner')->unique('id')->count();
    $staff = $records->where('category', 'Staff')->unique('id')->count();
    $aggregators = $records->where('category', 'Aggregator')->unique('id')->count();
    $transporters = $records->where('category', 'Transporter')->unique('id')->count();

    // Total unique individuals
    $total = $uniquePeople->count();

    return [
        'Total' => $total,
        'Cassava' => $cassava,
        'Sweet potato' => $sweetPotato,
        'Potato' => $potato,
        'Farmers' => $farmers,
        'Processors' => $processors,
        'Traders' => $traders,
        'Partner' => $partners,
        'Staff' => $staff,
        'Aggregators' => $aggregators,
        'Transporters' => $transporters
    ];
}
}
