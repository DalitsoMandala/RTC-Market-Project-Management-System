<?php

namespace App\Imports\rtcmarket\HouseholdImport;

use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Ramsey\Uuid\Uuid;

HeadingRowFormatter::default('none');

class HrcImport implements ToCollection, WithHeadingRow, WithEvents
{
    /**
     * @param Collection $collection
     */
    use Importable, RegistersEventListeners;
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
    public $expectedSheetNames = ['HH_CONSUMPTION'];
    public $location, $userId, $file;
    public function __construct($userId, $sheets, $file = null)
    {
        $this->userId = $userId;
        $this->sheetNames = $sheets;
        $this->file = $file;
    }
    public function collection(Collection $collection)
    {
        //
        $headings = (new HeadingRowImport)->toArray($this->file);

        $headings = $headings[0][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {
            throw new UserErrorException("Something went wrong. Please upload your data using the template file above");

        }

        try {
            $uuid = Uuid::uuid4()->toString();
            $main_data = [];

            foreach ($collection as $row) {

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
                    'uuid' => $uuid,
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

            session()->put('uuid', $uuid);
            session()->put('batch_data', $main_data);

        } catch (\Throwable $e) {
            throw new UserErrorException("Something went wrong. There was some errors on some rows." . $e->getMessage());
        }

    }

    // public function registerEvents(): array
    // {
    //     return [
    //         // Handle by a closure.
    //         BeforeImport::class => function (BeforeImport $event) {
    //             // dd($event);
    //             $diff = ImportValidateHeading::validateHeadings($this->sheetNames, $this->expectedSheetNames);

    //             if (count($diff) > 0) {
    //                 session()->flash('error-import', "File contains invalid sheets!");
    //                 throw new UserErrorException("File contains invalid sheets!");

    //             }
    //         },

    //     ];
    // }
    // private function validateHeadings(array $headings)
    // {
    //     // Collect missing headings
    //     $missingHeadings = [];
    //     foreach ($this->expectedHeadings as $expectedHeading) {
    //         if (!in_array($expectedHeading, $headings)) {
    //             $missingHeadings[] = $expectedHeading;
    //         }
    //     }
    //     return $missingHeadings;
    // }
}
