<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Jobs\sendtoTableJob;
use App\Models\JobProgress;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
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
class RpmFarmerImportSheet5 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure // INTER MARKETS
{
    public $userId;
    public $file;
    public $uuid;
    public $submissionData = [];
    public $failures = [];
    public $mappings = [];

    public $highestId;
    public $expectedHeadings = [
        'RECRUIT ID',
        'DATE RECORDED',
        'CROP TYPE',
        'MARKET NAME',
        'COUNTRY',
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
        $this->highestId = RtcProductionFarmer::max('id');
    }
    public function collection(Collection $collection)
    {

        if (!empty($this->failures)) {
            \Log::channel('system_log')->error('Import validation errors: ' . var_export($this->failures));

            throw new SheetImportException('RTC_FARM_FLUP', $this->failures);
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
                'rpm_farmer_id' => $row['RECRUIT ID'],
                'date_recorded' => $row['DATE RECORDED'],
                'crop_type' => $row['CROP TYPE'],
                'market_name' => $row['MARKET NAME'],
                'country' => $row['COUNTRY'],
                'date_of_maximum_sale' => $row['DATE OF MAXIMUM SALE'],
                'product_type' => $row['PRODUCT TYPE'],
                'volume_sold_previous_period' => $row['VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)'],
                'financial_value_of_sales' => $row['FINANCIAL VALUE OF SALES'],
                // 'user_id' => $this->userId,
                //  'uuid' => session()->get('uuid'),
            ];

        }




        $this->processBatch($batch, $this->submissionData, $uuid, $importJob, $this->highestId);


    }
    protected function processBatch($batch, $submissionData, $uuid, $importJob, $highestId)
    {

        $existingData = cache()->get("submissions.{$this->uuid}.intermarket", []);
        $mergedData = array_merge($existingData, $batch);
        cache()->put("submissions.{$this->uuid}.intermarket", $mergedData);

        $finalData = [
            'main' => cache()->get("submissions.{$this->uuid}.main"),
            'followup' => cache()->get("submissions.{$this->uuid}.followup"),
            'agreement' => cache()->get("submissions.{$this->uuid}.agreement"),
            'market' => cache()->get("submissions.{$this->uuid}.market"),
            'intermarket' => cache()->get("submissions.{$this->uuid}.intermarket"),
        ];

        $sub = $submissionData;
        unset($sub['submission_period_id']);
        unset($sub['organisation_id']);
        unset($sub['financial_year_id']);
        unset($sub['period_month_id']);
        $sub['batch_no'] = $uuid;
        $sub['data'] = json_encode($finalData);

        Submission::create($sub);


        foreach ($finalData['main'] as $data) {
            dd($data);

        }


        $progress = 100;
        cache()->put($uuid . '_progress', $progress);
        $importJob->update(['progress' => $progress, 'is_finished' => 1]);

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
            'RECRUIT ID' => ['required', 'integer', Rule::in($ids)],
            'DATE RECORDED' => ['required', 'date'],
            'CROP TYPE' => ['required', 'string'],
            'MARKET NAME' => ['required', 'string'],
            'COUNTRY' => ['required', 'string'],
            'DATE OF MAXIMUM SALE' => ['required', 'date'],
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


    }
}