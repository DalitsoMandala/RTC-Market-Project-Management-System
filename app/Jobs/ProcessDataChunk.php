<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessDataChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $rows;
    protected $submissionData;
    protected $uuid;

    public function __construct(Collection $rows, array $submissionData, $uuid)
    {
        $this->rows = $rows;
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
    }
    public function handle()
    {
        $submissionData = $this->submissionData;
        $modifiedRows = $this->rows->map(function ($row) use ($submissionData) {
            $uuid = (string) Str::uuid();

            $entry = [
                'submission_period_id' => $submissionData['submission_period_id'],
                'organisation_id' => $submissionData['organisation_id'],
                'financial_year_id' => $submissionData['financial_year_id'],
                'period_month_id' => $submissionData['period_month_id'],
                'location_data' => json_encode([
                    'epa' => $row['EPA'],
                    'district' => $row['DISTRICT'],
                    'section' => $row['SECTION'],
                    'enterprise' => $row['ENTERPRISE'],
                ]),
                'date_of_assessment' => $row['DATE OF ASSESSMENT'],
                'actor_type' => $row['ACTOR TYPE'],
                'rtc_group_platform' => $row['RTC GROUP PLATFORM'],
                'producer_organisation' => $row['PRODUCER ORGANISATION'],
                'actor_name' => $row['ACTOR NAME'],
                'age_group' => $row['AGE GROUP'],
                'sex' => $row['SEX'],
                'phone_number' => $row['PHONE NUMBER'],
                'household_size' => $row['HOUSEHOLD SIZE'],
                'under_5_in_household' => $row['UNDER 5 IN HOUSEHOLD'],
                'rtc_consumers' => $row['RTC CONSUMERS'],
                'rtc_consumers_potato' => $row['RTC CONSUMERS/POTATO'],
                'rtc_consumers_sw_potato' => $row['RTC CONSUMERS/SWEET POTATO'],
                'rtc_consumers_cassava' => $row['RTC CONSUMERS/CASSAVA'],
                'rtc_consumption_frequency' => $row['RTC CONSUMPTION FREQUENCY'],
                'user_id' => Auth::id(),
                'uuid' => $uuid,
                'main_food_data' => [],
            ];

            if ($row['RTC MAIN FOOD/CASSAVA'] === 'Yes') {
                $entry['main_food_data'][] = ['name' => 'CASSAVA'];
            }
            if ($row['RTC MAIN FOOD/POTATO'] === 'Yes') {
                $entry['main_food_data'][] = ['name' => 'POTATO'];
            }
            if ($row['RTC MAIN FOOD/SWEET POTATO'] === 'Yes') {
                $entry['main_food_data'][] = ['name' => 'SWEET POTATO'];
            }
            $entry['main_food_data'] = json_encode($entry['main_food_data']);

            return $entry;


        });


        // Store the processed rows in cache
        $existingData = Cache::get($this->uuid, []);
        Cache::put($this->uuid, array_merge($existingData, $modifiedRows->toArray()), now()->addMinutes(30));



    }
}
