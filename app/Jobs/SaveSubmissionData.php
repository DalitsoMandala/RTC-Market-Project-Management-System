<?php

namespace App\Jobs;

use App\Models\HouseholdRtcConsumption;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaveSubmissionData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $submissionData;
    protected $uuid;

    public function __construct(array $submissionData, $uuid)
    {
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
    }

    public function handle()
    {
        $modifiedRows = Cache::pull($this->uuid);
        $uuid = $this->uuid;
        $submissionData = $this->submissionData;
        DB::transaction(function () use ($modifiedRows, $uuid, $submissionData) {
            unset($submissionData['submission_period_id']);
            unset($submissionData['organisation_id']);
            unset($submissionData['financial_year_id']);
            unset($submissionData['period_month_id']);
            $submissionData['batch_no'] = $uuid;


            $submissionData['data'] = json_encode($modifiedRows);



            Submission::create($submissionData);
        });
    }
}
