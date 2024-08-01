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
class RpmProcessorImportSheet4 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public $userId;
    public $file;
    public $uuid;
    public $submissionData = [];
    public $failures = [];
    public $expectedHeadings = [
        'RECRUIT ID',
        'DATE RECORDED',
        'CROP TYPE',
        'MARKET NAME',
        'DISTRICT',
        'DATE OF MAXIMUM SALE',
        'PRODUCT TYPE',
        'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)',
        'FINANCIAL VALUE OF SALES',
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
            \Log::channel('system_log')->error('Import validation errors: ' . var_export($this->failures));

            throw new SheetImportException('RTC_FARM_DOM', $this->failures);
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
                'date_recorded' => $row['DATE RECORDED'],
                'crop_type' => $row['CROP TYPE'],
                'market_name' => $row['MARKET NAME'],
                'district' => $row['DISTRICT'],
                'date_of_maximum_sale' => $row['DATE OF MAXIMUM SALE'],
                'product_type' => $row['PRODUCT TYPE'],
                'volume_sold_previous_period' => $row['VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)'],
                'financial_value_of_sales' => $row['FINANCIAL VALUE OF SALES'],

            ];

        }

        $this->processBatch($batch, $this->submissionData, $uuid, $importJob);


    }
    protected function processBatch($batch, $submissionData, $uuid, $importJob)
    {


        $existingData = cache()->get("submissions.{$this->uuid}.market", []);
        $mergedData = array_merge($existingData, $batch);
        cache()->put("submissions.{$this->uuid}.market", $mergedData);

        $progress = 80;
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
            'DATE RECORDED' => ['required', 'date_format:Y-m-d'],
            'CROP TYPE' => ['nullable', 'string'],
            'MARKET NAME' => ['nullable', 'string'],
            'DISTRICT' => ['nullable', 'string'],
            'DATE OF MAXIMUM SALE' => ['required', 'date_format:Y-m-d'],
            'PRODUCT TYPE' => ['nullable', 'string'],
            'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)' => ['nullable', 'numeric'],
            'FINANCIAL VALUE OF SALES' => ['nullable', 'numeric'],
        ];
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
        throw new SheetImportException('SheetName', $errors); // Replace 'SheetName' with actual sheet name if possible
    }
}