<?php

namespace App\Jobs;

use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendtoTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $table;
    public $submissionData = [];

    public $data = [];

    public $mappings = [];

    public $highestId;

    public $uuid;
    /**
     * Create a new job instance.
     */
    public function __construct($uuid, $table, $submissionData)
    {
        //

        $this->uuid = $uuid;
        $this->table = $table;
        $this->submissionData = $submissionData;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        if ($this->table == 'rtc_production_farmers') {
            $submission = Submission::where('batch_no', $this->uuid)->where('status', 'approved')->get();
            foreach ($submission as $sub) {

                $data = json_decode($sub['data'], true);
                $idMappings = [];
                $highestId = RtcProductionFarmer::max('id');
                foreach ($data['main'] as $mainSheet) {
                    $highestId++;

                    $mainSheet['is_registered'] = $mainSheet['is_registered'] == 'YES' ? true : false;
                    $mainSheet['is_registered_seed_producer'] = $mainSheet['is_registered_seed_producer'] == 'YES' ? true : false;
                    $mainSheet['uses_certified_seed'] = $mainSheet['uses_certified_seed'] == 'YES' ? true : false;
                    $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] == 'YES' ? true : false;
                    $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] == 'YES' ? true : false;
                    $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] == 'YES' ? true : false;
                    $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] == 'YES' ? true : false;

                    $idMappings[$mainSheet['#']] = $highestId;
                    unset($mainSheet['#']);

                    $mainSheet['submission_period_id'] = $this->submissionData['submission_period_id'];
                    $mainSheet['organisation_id'] = $this->submissionData['organisation_id'];
                    $mainSheet['financial_year_id'] = $this->submissionData['financial_year_id'];
                    $mainSheet['period_month_id'] = $sub['period_month_id'];

                    RtcProductionFarmer::create($mainSheet);

                }

                foreach ($data['followup'] as $mainSheet) {
                    $newId = $idMappings[$mainSheet['rpm_farmer_id']];
                    $mainSheet['rpm_farmer_id'] = $newId;
                    $mainSheet['is_registered_seed_producer'] = $mainSheet['is_registered_seed_producer'] == 'YES' ? true : false;
                    $mainSheet['uses_certified_seed'] = $mainSheet['uses_certified_seed'] == 'YES' ? true : false;
                    //   $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] == 'YES' ? true : false;
                    // $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] == 'YES' ? true : false;
                    // $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] == 'YES' ? true : false;
                    //  $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] == 'YES' ? true : false;
                    $mainTable = RpmFarmerFollowUp::create($mainSheet);

                    // follow up data

                }

            }
        }


    }
}
