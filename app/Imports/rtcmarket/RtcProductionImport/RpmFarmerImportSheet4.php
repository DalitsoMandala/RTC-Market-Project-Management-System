<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Models\JobProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');
class RpmFarmerImportSheet4 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
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

            throw new SheetImportException('RTC_FARM_DOM', $this->failures);
        }

        $importJob = JobProgress::where('user_id', $this->userId)->where('job_id', $this->uuid)->where('is_finished', false)->first();
        if ($importJob) {
            $importJob->update(['status' => 'processing']);
        }




        if (empty($this->failures)) {

            $headings = ($collection->first()->keys())->toArray();
            $diff = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

            if (count($diff) > 0) {

                throw new UserErrorException("File contains invalid headings!");

            }


        }


        $submissionData = $this->submissionData;
        $uuid = $this->uuid;
        $batch = [];



        foreach ($collection as $row) {


            $batch[] = [
                'rpm_farmer_id' => $row['RECRUIT ID'],
                'date_recorded' => $row['DATE RECORDED'],
                'crop_type' => $row['CROP TYPE'],
                'market_name' => $row['MARKET NAME'],
                'district' => $row['DISTRICT'],
                'date_of_maximum_sale' => $row['DATE OF MAXIMUM SALE'],
                'product_type' => $row['PRODUCT TYPE'],
                'volume_sold_previous_period' => $row['VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)'],
                'financial_value_of_sales' => $row['FINANCIAL VALUE OF SALES'],
                // 'user_id' => $this->userId,
                //  'uuid' => session()->get('uuid'),
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
        $getBatchMainData = cache()->get("submissions.{$this->uuid}.main");
        $ids = array();
        if (!empty($getBatchMainData)) {
            $ids = collect($getBatchMainData)->pluck('#')->toArray();

        }
        return [
            'RECRUIT ID' => ['integer', Rule::in($ids)],
            'DATE RECORDED' => ['date'],
            'CROP TYPE' => ['string'],
            'MARKET NAME' => ['string'],
            'DISTRICT' => ['string'],
            'DATE OF MAXIMUM SALE' => ['date'],
            'PRODUCT TYPE' => ['string'],
            'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)' => ['numeric'],
            'FINANCIAL VALUE OF SALES' => ['numeric'],
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
