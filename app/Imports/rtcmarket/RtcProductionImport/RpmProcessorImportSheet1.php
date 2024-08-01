<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Models\JobProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Ramsey\Uuid\Nonstandard\Uuid;

HeadingRowFormatter::default('none');
class RpmProcessorImportSheet1 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public $userId;
    public $file;
    public $uuid;
    private $failures = [];
    public $submissionData = [];
    public $expectedHeadings = [
        '#',
        'ENTERPRISE',
        'DISTRICT',
        'EPA',
        'SECTION',
        'DATE OF RECRUITMENT',
        'NAME OF ACTOR',
        'NAME OF REPRESENTATIVE',
        'PHONE NUMBER',
        'TYPE',
        'APPROACH', // FOR PRODUCER ORGANIZATIONS ONLY
        'SECTOR',
        'NUMBER OF MEMBERS/TOTAL',
        'NUMBER OF MEMBERS/FEMALE 18-35YRS',
        'NUMBER OF MEMBERS/FEMALE 35YRS+',
        'NUMBER OF MEMBERS/MALE 18-35YRS',
        'NUMBER OF MEMBERS/MALE 35YRS+', // FOR PRODUCER ORGANIZATIONS ONLY
        'GROUP',
        'ESTABLISHMENT STATUS', // UPPERCASE FOR ENUM VALUES
        'IS REGISTERED',
        'REGISTRATION DETAILS/REGISTRATION BODY',
        'REGISTRATION DETAILS/REGISTRATION NUMBER',
        'REGISTRATION DETAILS/REGISTRATION DATE',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+',
        'MARKET SEGMENT/FRESH',
        'MARKET SEGMENT/PROCESSED', // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
        'HAS RTC MARKET CONTRACT',
        'TOTAL VOLUME PRODUCTION PREVIOUS SEASON',
        'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL',
        'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES',
        'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON',
        'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL',
        'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES',
        'SELLS TO DOMESTIC MARKETS',
        'SELLS TO INTERNATIONAL MARKETS',
        'USES MARKET INFORMATION SYSTEMS',
        'MARKET INFORMATION SYSTEMS',
        'SELLS TO AGGREGATION CENTERS',
        'AGGREGATION CENTERS/RESPONSE',
        'AGGREGATION CENTERS/SPECIFY',
        'TOTAL AGGREGATION CENTER SALES VOLUME',

    ];
    public function __construct($userId, $file, $uuid, $submissionData)
    {
        $this->userId = $userId;
        $this->file = $file;
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
    }

    public function collection(Collection $collection)
    {
        if (!empty($this->failures)) {
            Log::channel('system_log')->error('Import validation errors: ' . var_export($this->failures));
            throw new SheetImportException('RTC_PROCESSORS', $this->failures);
        }
        $importJob = JobProgress::where('user_id', $this->userId)->where('job_id', $this->uuid)->where('is_finished', false)->first();
        if ($importJob) {
            $importJob->update(['status' => 'processing']);
        }

        $submissionData = $this->submissionData;
        $uuid = $this->uuid;
        $batch = [];

        foreach ($collection as $row) {
            $batch[] = [
                '#' => $row['#'],
                'location_data' => json_encode([
                    'enterprise' => $row['ENTERPRISE'],
                    'district' => $row['DISTRICT'],
                    'epa' => $row['EPA'],
                    'section' => $row['SECTION'],
                ]),
                'date_of_recruitment' => $row['DATE OF RECRUITMENT'],
                'name_of_actor' => $row['NAME OF ACTOR'],
                'name_of_representative' => $row['NAME OF REPRESENTATIVE'],
                'phone_number' => $row['PHONE NUMBER'],
                'type' => $row['TYPE'],
                'approach' => $row['APPROACH'], // For producer organizations only
                'sector' => $row['SECTOR'],
                'number_of_members' => json_encode([
                    'total' => $row['NUMBER OF MEMBERS/TOTAL'],
                    'female_18_35' => $row['NUMBER OF MEMBERS/FEMALE 18-35YRS'],
                    'female_35_plus' => $row['NUMBER OF MEMBERS/FEMALE 35YRS+'],
                    'male_18_35' => $row['NUMBER OF MEMBERS/MALE 18-35YRS'],
                    'male_35_plus' => $row['NUMBER OF MEMBERS/MALE 35YRS+'],
                ]),
                'group' => $row['GROUP'],
                'establishment_status' => $row['ESTABLISHMENT STATUS'],
                'is_registered' => $row['IS REGISTERED'],
                'registration_details' => json_encode([
                    'registration_body' => $row['REGISTRATION DETAILS/REGISTRATION BODY'],
                    'registration_number' => $row['REGISTRATION DETAILS/REGISTRATION NUMBER'],
                    'registration_date' => $row['REGISTRATION DETAILS/REGISTRATION DATE'],
                ]),
                'number_of_employees' => json_encode([
                    'formal' => [
                        'total' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)'],
                        'female_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS'],
                        'female_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+'],
                        'male_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS'],
                        'male_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+'],
                    ],
                    'informal' => [
                        'total' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)'],
                        'female_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS'],
                        'female_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+'],
                        'male_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS'],
                        'male_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+'],
                    ],
                ]),
                'market_segment' => json_encode([
                    'fresh' => $row['MARKET SEGMENT/FRESH'],
                    'processed' => $row['MARKET SEGMENT/PROCESSED'],
                ]),
                'has_rtc_market_contract' => $row['HAS RTC MARKET CONTRACT'],
                'total_vol_production_previous_season' => $row['TOTAL VOLUME PRODUCTION PREVIOUS SEASON'],
                'total_production_value_previous_season' => json_encode([
                    'total' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL'],
                    'date_of_maximum_sales' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES'],
                ]),
                'total_vol_irrigation_production_previous_season' => $row['TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON'],
                'total_irrigation_production_value_previous_season' => json_encode([
                    'total' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL'],
                    'date_of_maximum_sales' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES'],
                ]),
                'sells_to_domestic_markets' => $row['SELLS TO DOMESTIC MARKETS'],
                'sells_to_international_markets' => $row['SELLS TO INTERNATIONAL MARKETS'],
                'uses_market_information_systems' => $row['USES MARKET INFORMATION SYSTEMS'],
                'market_information_systems' => $row['MARKET INFORMATION SYSTEMS'],
                'aggregation_centers' => json_encode([
                    'response' => $row['AGGREGATION CENTERS/RESPONSE'],
                    'specify' => $row['AGGREGATION CENTERS/SPECIFY'],
                ]),
                'aggregation_center_sales' => $row['TOTAL AGGREGATION CENTER SALES VOLUME'],
                'user_id' => $this->userId,
                'uuid' => $uuid,
            ];

        }

        $this->processBatch($batch, $this->submissionData, $uuid, $importJob);



    }

    public function rules(): array
    {
        return [
            '#' => ['required', 'numeric', 'distinct'],
            'ENTERPRISE' => ['nullable', 'string'],
            'DISTRICT' => ['nullable', 'string'],
            'EPA' => ['nullable', 'string'],
            'SECTION' => ['nullable', 'string'],
            'DATE OF RECRUITMENT' => ['nullable', 'date_format:Y-m-d'],
            'NAME OF ACTOR' => ['nullable', 'string'],
            'NAME OF REPRESENTATIVE' => ['nullable', 'string'],
            'PHONE NUMBER' => ['nullable', 'string'],
            'TYPE' => ['nullable', 'string'],
            'APPROACH' => ['nullable', 'string'],
            'SECTOR' => ['nullable', 'string'],
            'NUMBER OF MEMBERS/TOTAL' => ['nullable', 'integer'],
            'NUMBER OF MEMBERS/FEMALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF MEMBERS/FEMALE 35YRS+' => ['nullable', 'integer'],
            'NUMBER OF MEMBERS/MALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF MEMBERS/MALE 35YRS+' => ['nullable', 'integer'],
            'GROUP' => ['nullable', 'string'],
            'ESTABLISHMENT STATUS' => ['nullable', 'string'],
            'IS REGISTERED' => ['nullable', 'in:YES,NO'],
            'REGISTRATION DETAILS/REGISTRATION BODY' => ['nullable', 'string'],
            'REGISTRATION DETAILS/REGISTRATION NUMBER' => ['nullable', 'string'],
            'REGISTRATION DETAILS/REGISTRATION DATE' => ['nullable', 'date_format:Y-m-d'],
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS' => ['nullable', 'integer'],
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+' => ['nullable', 'integer'],
            'MARKET SEGMENT/FRESH' => ['nullable', 'in:YES,NO'],
            'MARKET SEGMENT/PROCESSED' => ['nullable', 'in:YES,NO'],
            'HAS RTC MARKET CONTRACT' => ['nullable', 'in:YES,NO'],
            'TOTAL VOLUME PRODUCTION PREVIOUS SEASON' => ['nullable', 'numeric'],
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => ['nullable', 'numeric'],
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => ['nullable', 'date_format:Y-m-d'],
            'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON' => ['nullable', 'numeric'],
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => ['nullable', 'numeric'],
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => ['nullable', 'date_format:Y-m-d'],
            'SELLS TO DOMESTIC MARKETS' => ['nullable', 'in:YES,NO'],
            'SELLS TO INTERNATIONAL MARKETS' => ['nullable', 'in:YES,NO'],
            'USES MARKET INFORMATION SYSTEMS' => ['nullable', 'in:YES,NO'],
            'MARKET INFORMATION SYSTEMS' => ['nullable', 'string'],
            'SELLS TO AGGREGATION CENTERS' => ['nullable', 'in:YES,NO'],
            'AGGREGATION CENTERS/RESPONSE' => ['nullable', 'string'],
            'AGGREGATION CENTERS/SPECIFY' => ['nullable', 'string'],
            'TOTAL AGGREGATION CENTER SALES VOLUME' => ['nullable', 'numeric'],
        ];
    }
    protected function processBatch($batch, $submissionData, $uuid, $importJob)
    {


        $existingData = cache()->get("submissions.{$this->uuid}.main", []);
        $mergedData = array_merge($existingData, $batch);
        cache()->put("submissions.{$this->uuid}.main", $mergedData);



        $progress = 20;
        cache()->put($uuid . '_progress', $progress);



        $importJob->update(['progress' => $progress]);




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


    }


}
