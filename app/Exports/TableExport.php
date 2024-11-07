<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TableExport implements FromCollection, WithMapping, WithHeadings, ShouldQueue
{
    protected $data;
    public function headings(): array
    {
        return [
            'Enterprise',
            'District',
            'EPA',
            'Section',
            'Date of assessment',
            'Actor type',
            'Rtc group platform',
            'Producer organisation',
            'Actor name',
            'Age group',
            'Sex',
            'Phone number',
            'Household size',
            'Under 5 in household',
            'Rtc consumers',
            'Rtc consumers/Potato',
            'Rtc consumers/Sweet Potato',
            'Rtc consumers/Cassava',
            'Rtc consumption frequency',
            'RTC MAIN FOOD/CASSAVA',
            'RTC MAIN FOOD/POTATO',
            'RTC MAIN FOOD/SWEET POTATO',
            'Submission Date',
            'Submitted By',
            'UUID',
        ];
    }
    public function __construct(Collection $data)
    {


        $this->data = $data->map(function ($item) {

            $location = json_decode($item->location_data);
            $main_food = json_decode($item->main_food_data);

            $item->enterprise = $location->enterprise ?? null;
            $item->district = $location->district ?? null;
            $item->epa = $location->epa ?? null;
            $item->section = $location->section ?? null;
            $item->date_of_assessment = Carbon::parse($item->date_of_assessment)->format('d/m/Y');

            $food = collect($main_food);
            $item->cassava_count = $food->contains('name', 'CASSAVA') ? 'Yes' : 'No';
            $item->potato_count = $food->contains('name', 'POTATO') ? 'Yes' : 'No';
            $item->sweet_potato_count = $food->contains('name', 'SWEET POTATO') ? 'Yes' : 'No';

            $item->submission_date = Carbon::parse($item->created_at)->format('d/m/Y');
            $item->submitted_by = $item->user->organisation->name;
            return $item;
        });
    }

    public function map($row): array
    {


        return [
            $row->enterprise,
            $row->district,
            $row->epa,
            $row->section,
            $row->date_of_assessment_formatted,
            $row->actor_type,
            $row->rtc_group_platform,
            $row->producer_organisation,
            $row->actor_name,
            $row->age_group,
            $row->sex,
            $row->phone_number,
            $row->household_size,
            $row->under_5_in_household,
            $row->rtc_consumers,
            $row->rtc_consumers_potato,
            $row->rtc_consumers_sw_potato,
            $row->rtc_consumers_cassava,
            $row->rtc_consumption_frequency,
            $row->cassava_count,
            $row->potato_count,
            $row->sweet_potato_count,
            $row->submission_date,
            $row->submitted_by,
            $row->uuid,
        ];
    }

    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        return $this->data;
    }
}
