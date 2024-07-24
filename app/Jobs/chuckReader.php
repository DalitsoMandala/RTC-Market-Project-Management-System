<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class chuckReader implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $dataChunk;
    protected $uuid;
    protected $userId;
    public function __construct(array $dataChunk, $uuid, $userId)
    {
        $this->dataChunk = $dataChunk;
        $this->uuid = $uuid;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        $main_data = [];

        foreach ($this->dataChunk as $row) {
            $entry = [
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
                'user_id' => $this->userId,
                'uuid' => $this->uuid,
                'main_food_data' => [],
            ];

            if ($row['RTC MAIN FOOD/CASSAVA'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'CASSAVA'];
            }
            if ($row['RTC MAIN FOOD/POTATO'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'POTATO'];
            }
            if ($row['RTC MAIN FOOD/SWEET POTATO'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'SWEET POTATO'];
            }
            $entry['main_food_data'] = json_encode($entry['main_food_data']);

            $main_data[] = $entry;
        }


        session()->put('uuid', $this->uuid);
        session()->put('batch_data', $main_data);

        // Store the processed data in the session or a temporary storage
        //   session()->push('batch_data_' . $this->uuid, $main_data);
    }
}
