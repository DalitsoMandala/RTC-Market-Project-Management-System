<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\AttendanceRegister;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_3_2
{
    use FilterableQuery;

    protected $financial_year, $reporting_period, $organisation_id, $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }

    public function builder(): Builder
    {
        $query = AttendanceRegister::query()
            ->where('status', 'approved')
            ->where('meetingCategory', 'Training')
            ->whereIn('category', ['Farmer', 'Processor', 'Trader', 'Partner', 'Staff', 'Aggregator', 'Transporter'])
            ->select([
                'id', // Assuming 'id' is the unique person identifier
                'category',
                'rtcCrop_cassava',
                'rtcCrop_potato',
                'rtcCrop_sweet_potato',
            ]);

        return $this->applyAttendanceFilters($query);
    }

    public function getDisaggregations(): array
    {
        $records = $this->builder()->get();

        // 1. Calculate Crop Counts (Unique people per crop)
        // If a person is in both Cassava and Potato, they count +1 for each.
        $cassava     = $records->where('rtcCrop_cassava', true)->unique('id')->count();
        $potato      = $records->where('rtcCrop_potato', true)->unique('id')->count();
        $sweetPotato = $records->where('rtcCrop_sweet_potato', true)->unique('id')->count();

        // 2. Define the Grand Total as the sum of crops
        $grandTotal = $cassava + $potato + $sweetPotato;

        // 3. Category Counts
        // To make categories sum up to the Grand Total, we must check each category
        // against EACH crop flag.
        $categories     = ['Farmer', 'Processor', 'Trader', 'Partner', 'Staff', 'Aggregator', 'Transporter'];
        $categoryCounts = [];

        foreach ($categories as $cat) {
            $countForCat = 0;

            // Count unique individuals for this category in each crop segment
            $countForCat += $records->where('category', $cat)->where('rtcCrop_cassava', true)->unique('id')->count();
            $countForCat += $records->where('category', $cat)->where('rtcCrop_potato', true)->unique('id')->count();
            $countForCat += $records->where('category', $cat)->where('rtcCrop_sweet_potato', true)->unique('id')->count();

            $categoryCounts[$cat] = $countForCat;
        }

        return [
            'Total'        => 0,
            'Cassava'      => 0,
            'Potato'       => 0,
            'Sweet potato' => 0,
            'Farmers'      => 0,
            'Processors'   => 0,
            'Traders'      => 0,
            'Partner'      => 0,
            'Staff'        => 0,
            'Aggregators'  => 0,
            'Transporters' => 0,
        ];
    }
}
