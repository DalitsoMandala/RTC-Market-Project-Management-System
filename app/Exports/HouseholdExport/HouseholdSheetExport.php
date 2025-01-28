<?php

namespace App\Exports\HouseholdExport;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\HouseholdRtcConsumption;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HouseholdSheetExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    protected $uuid;
    protected $userId;
    protected $submissionPeriodId;
    protected $organisationId;
    protected $financialYearId;
    protected $reportingPeriodMonthId;
    protected $status;
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }


    public function headings(): array
    {
        return [
            'ID',
            'EPA',
            'Section',
            'District',
            'Enterprise',
            'Date of Assessment',
            'Actor Type (Farmer, Trader, etc.)',
            'RTC Group/Platform',
            'Producer Organisation',
            'Actor Name',
            'Age Group',
            'Sex',
            'Phone Number',
            'Household Size',
            'Under 5 in Household',
            'RTC Consumers (Total)',
            'RTC Consumers - Potato',
            'RTC Consumers - Sweet Potato',
            'RTC Consumers - Cassava',
            'RTC Consumption Frequency',

        ];
    }

    public function collection(): Collection
    {

        if ($this->template) {
            return collect([]);
        }
        $data = HouseholdRtcConsumption::select([
            'id',
            'epa',
            'section',
            'district',
            'enterprise',
            'date_of_assessment',
            'actor_type',
            'rtc_group_platform',
            'producer_organisation',
            'actor_name',
            'age_group',
            'sex',
            'phone_number',
            'household_size',
            'under_5_in_household',
            'rtc_consumers',
            'rtc_consumers_potato',
            'rtc_consumers_sw_potato',
            'rtc_consumers_cassava',
            'rtc_consumption_frequency',

            // Exclude hidden fields and Household ID
        ])->get();

        $data->transform(function ($row) {
            $row->date_of_assessment = Carbon::parse($row->date_of_assessment)->format('d-m-Y');
            return $row;
        });
        return $data;
    }

    public function title(): string
    {
        return 'Household Data';
    }
}
