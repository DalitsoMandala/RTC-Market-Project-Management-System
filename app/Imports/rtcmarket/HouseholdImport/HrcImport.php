<?php

namespace App\Imports\rtcmarket\HouseholdImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ArrayToUpperCase;
use App\Helpers\ImportValidateHeading;
use App\Jobs\chuckReader;
use App\Models\User;
use App\Notifications\JobNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Ramsey\Uuid\Uuid;

HeadingRowFormatter::default('none');

class HrcImport implements ToCollection, WithHeadingRow, WithEvents, WithValidation, SkipsOnFailure, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    use Importable, RegistersEventListeners;
    private $failures = [];

    protected $errors = [];
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
        $headings = (new HeadingRowImport)->toArray($this->file);
        $headings = $headings[0][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);
        if (count($missingHeadings) > 0) {
            throw new UserErrorException("Something went wrong. Please upload your data using the template file above");
        }

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


    }


    public function chunkSize(): int
    {
        return 1000; // Adjust the chunk size based on your requirements
    }
    public function onFailure(Failure ...$failures)
    {

        $errors = [];
        foreach ($failures as $failure) {
            $errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }



        throw new SheetImportException('HH_CONSUMPTION', $errors);

    }


    public function registerEvents(): array
    {
        return [
                // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                // dd($event);
                $diff = ImportValidateHeading::validateHeadings($this->sheetNames, $this->expectedSheetNames);

                if (count($diff) > 0) {

                    throw new UserErrorException("File contains invalid sheets!");

                }

                $sheets = $event->reader->getTotalRows();

                foreach ($sheets as $key => $sheet) {

                    if ($key == 'HH_CONSUMPTION') {
                        if ($sheet <= 1) {
                            throw new UserErrorException("The first sheet can not contain empty rows!");
                        }

                    }
                }




            },

        ];
    }
    public function rules(): array
    {
        return [
            '*.EPA' => 'string|max:255|nullable',
            '*.DISTRICT' => 'string|max:255|nullable',
            '*.SECTION' => 'string|max:255|nullable',
            '*.ENTERPRISE' => 'string|max:255|nullable',
            '*.DATE OF ASSESSMENT' => 'date|nullable',
            '*.ACTOR TYPE' => 'string|max:255|in:FARMER,PROCESSOR,TRADER,INDIVIDUALS FROM NUTRITION INTERVENTION,OTHER|nullable',
            '*.RTC GROUP PLATFORM' => 'string|max:255|nullable',
            '*.PRODUCER ORGANISATION' => 'string|max:255|nullable',
            '*.ACTOR NAME' => 'string|max:255|nullable',
            '*.AGE GROUP' => 'string|max:255|in:YOUTH,NOT YOUTH|nullable',
            '*.SEX' => 'string|in:MALE,FEMALE|nullable',
            '*.PHONE NUMBER' => 'string|max:255|nullable',
            '*.HOUSEHOLD SIZE' => 'numeric|min:1|nullable',
            '*.UNDER 5 IN HOUSEHOLD' => 'integer|min:0|nullable',
            '*.RTC CONSUMERS' => 'numeric|min:0|nullable',
            '*.RTC CONSUMERS/POTATO' => 'numeric|min:0|nullable',
            '*.RTC CONSUMERS/SWEET POTATO' => 'integer|min:0|nullable',
            '*.RTC CONSUMERS/CASSAVA' => 'numeric|min:0|nullable',
            '*.RTC CONSUMPTION FREQUENCY' => 'numeric|max:255|nullable',
            '*.RTC MAIN FOOD/CASSAVA' => 'string|in:YES,NO|nullable',
            '*.RTC MAIN FOOD/POTATO' => 'string|in:YES,NO|nullable',
            '*.RTC MAIN FOOD/SWEET POTATO' => 'string|in:YES,NO|nullable',
        ];
    }



}
