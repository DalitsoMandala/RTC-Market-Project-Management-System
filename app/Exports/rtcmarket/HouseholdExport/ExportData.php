<?php

namespace App\Exports\rtcmarket\HouseholdExport;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ExportData implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithChunkReading
{

    public function query()
    {
        return HouseholdRtcConsumption::query()->with(['mainFoods']);
    }

    public function headings(): array
    {
        return [
            'ENTERPRISE',
            'DISTRICT',
            'EPA',
            'SECTION',
            'DATE OF ASSESSMENT',
            'ACTOR TYPE',
            'RTC GROUP PLATFORM',
            'PRODUCER ORGANISATION',
            'ACTOR NAME',
            'AGE GROUP',
            'SEX',
            'PHONE NUMBER',
            'HOUSEHOLD SIZE',
            'UNDER 5 IN HOUSEHOLD',
            'RTC CONSUMERS',
            'RTC CONSUMERS/POTATO',
            'RTC CONSUMERS/SWEET POTATO',
            'RTC CONSUMERS/CASSAVA',
            'RTC CONSUMPTION FREQUENCY',
            'RTC MAIN FOOD/CASSAVA',
            'RTC MAIN FOOD/POTATO',
            'RTC MAIN FOOD/SWEET POTATO',

        ];

    }

    public function map($household): array
    {

        return [
            $household->enterprise ?? null,
            $household->district ?? null,
            $household->epa ?? null,
            $household->section ?? null,
            $household->date_of_assessment ?? null,
            $household->actor_type ?? null,
            $household->rtc_group_platform ?? null,
            $household->producer_organisation ?? null,
            $household->actor_name ?? null,
            $household->age_group ?? null,
            $household->sex ?? null,
            $household->phone_number ?? null,
            $household->household_size ?? null,
            $household->under_5_in_household ?? null,
            $household->rtc_consumers ?? null,
            $household->rtc_consumers_potato ?? null,
            $household->rtc_consumers_sw_potato ?? null,
            $household->rtc_consumers_cassava ?? null,
            $household->rtc_consumption_frequency ?? null,
            $household->mainFoods->pluck('name')->contains('Cassava') ? 'Cassava' : '',
            $household->mainFoods->pluck('name')->contains('Potato') ? 'Potato' : '',
            $household->mainFoods->pluck('name')->contains('Sweet potato') ? 'Sweet potato' : '',


        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
    public function title(): string
    {
        return 'HH_CONSUMPTION';
    }
}
