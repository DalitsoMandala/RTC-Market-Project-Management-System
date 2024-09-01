<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Models\JobProgress;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');
class RpmProcessorImportSheet2 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public $userId;
    public $file;
    public $uuid;
    public $submissionData = [];
    private $failures = [];
    // follow up

    public function __construct($userId, $file, $uuid, $submissionData)
    {
        $this->userId = $userId;
        $this->file = $file;
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
    }
    public $expectedHeadings = [
        'RECRUIT ID',
        'ENTERPRISE',
        'GROUP NAME',
        'DISTRICT',
        'EPA',
        'SECTION',
        'MARKET SEGMENT/Fresh',
        'MARKET SEGMENT/Processed', // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
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

    public function collection(Collection $collection)
    {

        if (!empty($this->failures)) {

            throw new SheetImportException('RTC_PROC_FLUP', $this->failures);
        }
        if (empty($this->failures)) {

            $headings = ($collection->first()->keys())->toArray();
            $diff = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

            if (count($diff) > 0) {

                throw new UserErrorException("File contains invalid headings!");

            }


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
                'rpm_processor_id' => $row['RECRUIT ID'],
                'location_data' => json_encode([
                    'enterprise' => $row['ENTERPRISE'],
                    'district' => $row['DISTRICT'],
                    'epa' => $row['EPA'],
                    'section' => $row['SECTION'],
                    'group_name' => $row['GROUP NAME'],
                ]),
                'date_of_follow_up' => $row['DATE OF FOLLOW UP'],
                'market_segment' => json_encode([
                    'fresh' => $row['MARKET SEGMENT/Fresh'],
                    'processed' => $row['MARKET SEGMENT/Processed'],
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
            ];

        }

        $this->processBatch($batch, $this->submissionData, $uuid, $importJob);

    }
    protected function processBatch($batch, $submissionData, $uuid, $importJob)
    {


        $existingData = cache()->get("submissions.{$this->uuid}.followup", []);
        $mergedData = array_merge($existingData, $batch);
        cache()->put("submissions.{$this->uuid}.followup", $mergedData);

        $progress = 40;
        cache()->put($uuid . '_progress', $progress);



        $importJob->update(['progress' => $progress]);


    }

    public function rules(): array
    {

        $main_data = [];
        $getBatchMainData = session()->get('batch_data');
        $ids = array();
        if (!empty($getBatchMainData['main'])) {
            $ids = collect($getBatchMainData['main'])->pluck('#')->toArray();

        }

        return [
            'RECRUIT ID' => ['required', 'integer'],
            'ENTERPRISE' => ['nullable', 'string'],
            'GROUP NAME' => ['nullable', 'string'],
            'DISTRICT' => ['nullable', 'string'],
            'EPA' => ['nullable', 'string'],
            'SECTION' => ['nullable', 'string'],
            'MARKET SEGMENT/Fresh' => ['nullable', 'in:Yes,No'],
            'MARKET SEGMENT/Processed' => ['nullable', 'in:Yes,No'],
            'HAS RTC MARKET CONTRACT' => ['nullable', 'in:Yes,No'],
            'TOTAL VOLUME PRODUCTION PREVIOUS SEASON' => ['nullable', 'numeric'],
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => ['nullable', 'numeric'],
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => ['nullable', 'date_format:Y-m-d'],
            'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON' => ['nullable', 'numeric'],
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => ['nullable', 'numeric'],
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => ['nullable', 'date_format:Y-m-d'],
            'SELLS TO DOMESTIC MARKETS' => ['nullable', 'in:Yes,No'],
            'SELLS TO INTERNATIONAL MARKETS' => ['nullable', 'in:Yes,No'],
            'USES MARKET INFORMATION SYSTEMS' => ['nullable', 'in:Yes,No'],
            'MARKET INFORMATION SYSTEMS' => ['nullable', 'string'],
            'SELLS TO AGGREGATION CENTERS' => ['nullable', 'in:Yes,No'],
            'AGGREGATION CENTERS/RESPONSE' => ['nullable', 'string'],
            'AGGREGATION CENTERS/SPECIFY' => ['nullable', 'string'],
            'TOTAL AGGREGATION CENTER SALES VOLUME' => ['nullable', 'numeric'],
        ];
    }
    public function onFailure(Failure ...$failures)
    {

        $errors = [];
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }


    }

}
