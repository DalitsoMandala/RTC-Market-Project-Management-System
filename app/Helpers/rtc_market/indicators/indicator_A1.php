<?php
namespace App\Helpers\rtc_market\indicators;

use App\Helpers\rtc_market\indicators\base;
use App\Models\Recruitment;
use Illuminate\Database\Eloquent\Builder;

class indicator_A1 extends base
{
    public function builder(): Builder
    {
        return $this->applyFilters(
            Recruitment::query()->where('status', 'approved')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN INDICATOR DISAGGREGATIONS
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        // 1. Database query for non-Traders
        $peopleStats = $this->builder()
            ->where('type', '!=', 'Traders')
            ->selectRaw("
                type,
                enterprise,
                establishment_status,
                SUM(mem_female_18_35 + mem_female_35_plus + mem_male_18_35 + mem_male_35_plus) as members_count,
                SUM(profit_female_18_35 + profit_female_35_plus + profit_male_18_35 + profit_male_35_plus) as profit_count,
                SUM(emp_formal_female_18_35 + emp_formal_female_35_plus + emp_formal_male_18_35 + emp_formal_male_35_plus) as formal_employees_count,
                SUM(emp_informal_female_18_35 + emp_informal_female_35_plus + emp_informal_male_18_35 + emp_informal_male_35_plus) as informal_employees_count
            ")
            ->groupBy('type', 'enterprise', 'establishment_status')
            ->get();

        // 2. Query for Traders (Counts & Establishment status breakdown)
        $traderStats = $this->builder()
            ->where('type', 'Traders')
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN enterprise = 'Cassava' THEN 1 END) as cassava,
                COUNT(CASE WHEN enterprise = 'Potato' THEN 1 END) as potato,
                COUNT(CASE WHEN enterprise = 'Sweet potato' THEN 1 END) as sweet_potato,
                COUNT(CASE WHEN establishment_status = 'New' THEN 1 END) as new_count,
                COUNT(CASE WHEN establishment_status = 'Old' THEN 1 END) as old_count
            ")
            ->first();

        // Initialize trackers
        $actors         = ['Farmers' => 0, 'Processors' => 0, 'Aggregators' => 0, 'Transporters' => 0];
        $cropPeople     = ['Cassava' => 0, 'Potato' => 0, 'Sweet potato' => 0];
        $establishments = ['New' => 0, 'Old' => 0];

        $totalMembers                  = 0;
        $totalProfitablyEngagedMembers = 0;
        $totalFormalEmployees          = 0;
        $totalInformalEmployees        = 0;

        // Process non-trader aggregates
        foreach ($peopleStats as $row) {
            $mCount        = (int) $row->members_count;
            $mPCount       = (int) $row->profit_count;
            $formalCount   = (int) $row->formal_employees_count;
            $informalCount = (int) $row->informal_employees_count;

            $engagedInRow = $mPCount + $formalCount;

            // Actor disaggregations (Profitably Engaged Members + Formal Employees)
            if (array_key_exists($row->type, $actors)) {
                $actors[$row->type] += $engagedInRow;
            }

            // Crop disaggregations (Profitably Engaged Members + Formal Employees)
            if (array_key_exists($row->enterprise, $cropPeople)) {
                $cropPeople[$row->enterprise] += $engagedInRow;
            }

            // Establishment status disaggregations (Profitably Engaged Members + Formal Employees)
            if (array_key_exists($row->establishment_status, $establishments)) {
                $establishments[$row->establishment_status] += $engagedInRow;
            }

            $totalMembers                  += $mCount;
            $totalProfitablyEngagedMembers += $mPCount;
            $totalFormalEmployees          += $formalCount;
            $totalInformalEmployees        += $informalCount;
        }

        $totalEmployees = $totalFormalEmployees + $totalInformalEmployees;
        $tradersTotal   = $traderStats->total ?? 0;

        // Formula: Total Profitably Engaged People
        $totalProfitablyEngagedPeople = $totalProfitablyEngagedMembers + $totalFormalEmployees + $tradersTotal;

        // Crop Totals = Non-Trader (Engaged Members + Formal Employees) + Traders
        $cropTotals  = [
            'Cassava'      => $cropPeople['Cassava'] + ($traderStats->cassava ?? 0),
            'Potato'       => $cropPeople['Potato'] + ($traderStats->potato ?? 0),
            'Sweet potato' => $cropPeople['Sweet potato'] + ($traderStats->sweet_potato ?? 0),
        ];

        // Establishment Totals = Non-Trader (Engaged Members + Formal Employees) + Traders
        $establishmentTotals = [
            'New' => $establishments['New'] + ($traderStats->new_count ?? 0),
            'Old' => $establishments['Old'] + ($traderStats->old_count ?? 0),
        ];

        // Map disaggregations collection
        $disaggregations = $this->getIndicatorDisaggregations('A1');

        // Total
        $disaggregations->put('Total', $totalProfitablyEngagedPeople);

        // Actors disaggregation sum = Total
        $disaggregations->put('Farmers', $actors['Farmers']);
        $disaggregations->put('Processors', $actors['Processors']);
        $disaggregations->put('Aggregators', $actors['Aggregators']);
        $disaggregations->put('Transporters', $actors['Transporters']);
        $disaggregations->put('Traders', $tradersTotal);

        // Crops disaggregation sum = Total
        $disaggregations->put('Cassava', $cropTotals['Cassava']);
        $disaggregations->put('Potato', $cropTotals['Potato']);
        $disaggregations->put('Sweet potato', $cropTotals['Sweet potato']);

        // Establishments disaggregation sum = Total
        $disaggregations->put('New establishment', $establishmentTotals['New']);
        $disaggregations->put('Old establishment', $establishmentTotals['Old']);

        // Breakdown disaggregations
        $disaggregations->put('Employees', $totalEmployees);

        return $disaggregations->toArray();
    }
}
