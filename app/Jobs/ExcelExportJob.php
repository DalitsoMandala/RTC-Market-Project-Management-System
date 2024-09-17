<?php

namespace App\Jobs;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\rtcmarket\HrcExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\rtcmarket\HouseholdExport\ExportData;
use Illuminate\Support\Facades\Cache; // Use Cache for progress tracking

class ExcelExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $name;
    public $uniqueID;
    public $progressKey;
    public $statusKey;

    /**
     * Create a new job instance.
     */
    public function __construct($name, $uniqueID)
    {
        $this->name = $name;
        $this->uniqueID = $uniqueID;


    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->name === 'hrc') {
            $filePath = storage_path('app/public/exports/household-rtc-consumption_' . $this->uniqueID . '.xlsx');
            // Define the headers
            $headers = [
                'Enterprise',
                'District',
                'EPA',
                'Section',
                'Date of Assessment',
                'Actor Type',
                'RTC Group Platform',
                'Producer Organisation',
                'Actor Name',
                'Age Group',
                'Sex',
                'Phone Number',
                'Household Size',
                'Under 5 in Household',
                'RTC Consumers',
                'RTC Consumers Potato',
                'RTC Consumers Sweet Potato',
                'RTC Consumers Cassava',
                'RTC Consumption Frequency',
                'Cassava Main Food',
                'Potato Main Food',
                'Sweet Potato Main Food',
            ];

            // Create a new SimpleExcelWriter instance
            $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

            // Process data in chunks
            HouseholdRtcConsumption::chunk(1000, function ($households) use ($writer) {
                foreach ($households as $household) {
                    $writer->addRow([
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
                        $household->mainFoods->pluck('name')->contains('Cassava') ? 1 : 0,
                        $household->mainFoods->pluck('name')->contains('Potato') ? 1 : 0,
                        $household->mainFoods->pluck('name')->contains('Sweet potato') ? 1 : 0,
                    ]);
                }
            });
            $writer->close(); // Finalize the file
        }
    }
}
