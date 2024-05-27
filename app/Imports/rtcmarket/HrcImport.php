<?php

namespace App\Imports\rtcmarket;

use App\Models\HouseholdRtcConsumption;
use App\Models\HrcMainFood;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class HrcImport implements ToCollection, WithHeadingRow, WithEvents
{
    /**
     * @param Collection $collection
     */
    use RegistersEventListeners;

    private $failures = [];
    protected $expectedHeadings = [
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

    public $sheetNames = [];
    public $location, $userId;
    public function __construct($location, $userId)
    {
        $this->location = $location;
        $this->userId = $userId;
    }
    public function collection(Collection $collection)
    {
        //
        $headings = $collection->first()->keys()->toArray();

        // Check if the headings match the expected headings
        $missingHeadings = $this->validateHeadings($headings);

        if (count($missingHeadings) > 0) {
            throw new \Exception("Something went wrong. Please upload your data using the template file above");

        }

        //  $currentSheetName = end($this->sheetNames);

// Process the rows if headings are valid
        try {
            $uuid = Str::random() . '_' . $this->userId;
            foreach ($collection as $row) {
                $hrc = HouseholdRtcConsumption::create([
                    'location_id' => $this->location,
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
                    'uuid' => $uuid,
                ]);
                if ($row['RTC MAIN FOOD/CASSAVA'] == 'Yes') {
                    HrcMainFood::create([
                        'name' => 'CASSAVA',
                        'hrc_id' => $hrc->id,
                    ]);

                }

                if ($row['RTC MAIN FOOD/POTATO'] == 'Yes') {
                    HrcMainFood::create([
                        'name' => 'POTATO',
                        'hrc_id' => $hrc->id,
                    ]);

                }

                if ($row['RTC MAIN FOOD/SWEET POTATO'] == 'Yes') {
                    HrcMainFood::create([
                        'name' => 'SWEET POTATO',
                        'hrc_id' => $hrc->id,
                    ]);

                }

            }

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows." . $e->getMessage());
        }

    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetNames[] = $event->getSheet()->getDelegate()->getTitle();
            },
        ];
    }
    private function validateHeadings(array $headings)
    {
        // Collect missing headings
        $missingHeadings = [];
        foreach ($this->expectedHeadings as $expectedHeading) {
            if (!in_array($expectedHeading, $headings)) {
                $missingHeadings[] = $expectedHeading;
            }
        }
        return $missingHeadings;
    }
}
