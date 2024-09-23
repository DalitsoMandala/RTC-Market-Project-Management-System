<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerBasicSeed;
use App\Models\RpmFarmerDomMarket;
use App\Models\RtcProductionFarmer;
use App\Exports\rtcmarket\HrcExport;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\SchoolRtcConsumption;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmFarmerCertifiedSeed;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RtcProductionProcessor;
use Illuminate\Queue\SerializesModels;
use App\Models\HouseholdRtcConsumption;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmFarmerAreaCultivation;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RpmProcessorConcAgreement;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\RpmFarmerAggregationCenter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\RpmProcessorAggregationCenter;
use App\Exports\rtcmarket\HouseholdExport\ExportData;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\Cache; // Use Cache for progress tracking

class ExcelExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $name;
    public $uniqueID;
    public $progressKey;
    public $statusKey;

    /**
     * Create a new job instance.
     */
    public function __construct($name, $uniqueID)
    {
        $this->name = $name;
        $this->uniqueID = $uniqueID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        switch ($this->name) {
            case 'hrc':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Enterprise',
                    'District',
                    'EPA',
                    'Section',
                    'Date of Assessment',
                    'Actor Type',
                    'RTC Group Platform',
                    'Producer Organisation',
                    'Actor Name',
                    'Age Group',
                    'Sex',
                    'Phone Number',
                    'Household Size',
                    'Under 5 in Household',
                    'RTC Consumers',
                    'RTC Consumers Potato',
                    'RTC Consumers Sweet Potato',
                    'RTC Consumers Cassava',
                    'RTC Consumption Frequency',
                    'Cassava Main Food',
                    'Potato Main Food',
                    'Sweet Potato Main Food',
                    'Submitted by'
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);



                // Process data in chunks
                HouseholdRtcConsumption::chunk(2000, function ($households) use ($writer) {
                    foreach ($households as $household) {

                        $submittedBy = '';
                        $user = User::find($household->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }
                        $writer->addRow([
                            $household->enterprise ?? null,
                            $household->district ?? null,
                            $household->epa ?? null,
                            $household->section ?? null,
                            $household->date_of_assessment ?? null,
                            $household->actor_type ?? null,
                            $household->rtc_group_platform ?? null,
                            $household->producer_organisation ?? null,
                            $household->actor_name ?? null,
                            $household->age_group ?? null,
                            $household->sex ?? null,
                            $household->phone_number ?? null,
                            $household->household_size ?? null,
                            $household->under_5_in_household ?? null,
                            $household->rtc_consumers ?? null,
                            $household->rtc_consumers_potato ?? null,
                            $household->rtc_consumers_sw_potato ?? null,
                            $household->rtc_consumers_cassava ?? null,
                            $household->rtc_consumption_frequency ?? null,
                            $household->mainFoods->pluck('name')->contains('Cassava') ? 1 : 0,
                            $household->mainFoods->pluck('name')->contains('Potato') ? 1 : 0,
                            $household->mainFoods->pluck('name')->contains('Sweet potato') ? 1 : 0,
                            $submittedBy
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'rpmf':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [

                    'Actor ID',
                    'EPA',
                    'Section',
                    'District',
                    'Enterprise',
                    'Date of Recruitment',
                    'Name of Actor',
                    'Name of Representative',
                    'Phone Number',
                    'Type',
                    'Approach',
                    'Sector',
                    'Members (Female 18-35)',
                    'Members (Male 18-35)',
                    'Members (Male 35+)',
                    'Members (Female 35+)',
                    'Group',
                    'Establishment Status',
                    'Is Registered',
                    'Registration Body',
                    'Registration Number',
                    'Registration Date',
                    'Employees (Formal Female 18-35)',
                    'Employees (Formal Male 18-35)',
                    'Employees (Formal Male 35+)',
                    'Employees (Formal Female 35+)',
                    'Employees (Informal Female 18-35)',
                    'Employees (Informal Male 18-35)',
                    'Employees (Informal Male 35+)',
                    'Employees (Informal Female 35+)',
                    'Number of Plantlets Produced (Cassava)',
                    'Number of Plantlets Produced (Potato)',
                    'Number of Plantlets Produced (Sweet Potato)',
                    'Screen House Vines Harvested',
                    'Screen House Min Tubers Harvested',
                    'Sah Plants Produced',
                    'Is Registered Seed Producer',
                    'Registration Number (Seed Producer)',
                    'Registration Date (Seed Producer)',
                    'Uses Certified Seed',
                    'Market Segment (Fresh)',
                    'Market Segment (Processed)',
                    'Has RTC Market Contract',
                    'Total Volume Production (Previous Season)',
                    'Production Value (Previous Season Total)',
                    'Production Value (Date of Max Sales)',
                    'Production Value (USD Rate)',
                    'Production Value (USD Value)',
                    'Total Volume Irrigation Production (Previous Season)',
                    'Irrigation Production Value (Previous Season Total)',
                    'Irrigation Production Value (Date of Max Sales)',
                    'Irrigation Production Value (USD Rate)',
                    'Irrigation Production Value (USD Value)',
                    'Sells to Domestic Markets',
                    'Sells to International Markets',
                    'Uses Market Information Systems',
                    'Sells to Aggregation Centers',
                    'Total Volume Aggregation Center Sales',
                    'Submitted by'
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RtcProductionFarmer::chunk(2000, function ($households) use ($writer) {
                    foreach ($households as $household) {
                        $submittedBy = '';
                        $user = User::find($household->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }
                        $writer->addRow([

                            $household->pf_id,
                            $household->epa,
                            $household->section,
                            $household->district,
                            $household->enterprise,
                            $household->date_of_recruitment,
                            $household->name_of_actor,
                            $household->name_of_representative,
                            $household->phone_number,
                            $household->type,
                            $household->approach,
                            $household->sector,
                            $household->mem_female_18_35,
                            $household->mem_male_18_35,
                            $household->mem_male_35_plus,
                            $household->mem_female_35_plus,
                            $household->group,
                            $household->establishment_status,
                            $household->is_registered,
                            $household->registration_body,
                            $household->registration_number,
                            $household->registration_date,
                            $household->emp_formal_female_18_35,
                            $household->emp_formal_male_18_35,
                            $household->emp_formal_male_35_plus,
                            $household->emp_formal_female_35_plus,
                            $household->emp_informal_female_18_35,
                            $household->emp_informal_male_18_35,
                            $household->emp_informal_male_35_plus,
                            $household->emp_informal_female_35_plus,
                            $household->number_of_plantlets_produced_cassava,
                            $household->number_of_plantlets_produced_potato,
                            $household->number_of_plantlets_produced_sweet_potato,
                            $household->number_of_screen_house_vines_harvested,
                            $household->number_of_screen_house_min_tubers_harvested,
                            $household->number_of_sah_plants_produced,
                            $household->is_registered_seed_producer,
                            $household->registration_number_seed_producer,
                            $household->registration_date_seed_producer,
                            $household->uses_certified_seed,
                            $household->market_segment_fresh,
                            $household->market_segment_processed,
                            $household->has_rtc_market_contract,
                            $household->total_vol_production_previous_season,
                            $household->prod_value_previous_season_total,
                            $household->prod_value_previous_season_date_of_max_sales,
                            $household->prod_value_previous_season_usd_rate,
                            $household->prod_value_previous_season_usd_value,
                            $household->total_vol_irrigation_production_previous_season,
                            $household->irr_prod_value_previous_season_total,
                            $household->irr_prod_value_previous_season_date_of_max_sales,
                            $household->irr_prod_value_previous_season_usd_rate,
                            $household->irr_prod_value_previous_season_usd_value,
                            $household->sells_to_domestic_markets,
                            $household->sells_to_international_markets,
                            $household->uses_market_information_systems,
                            $household->sells_to_aggregation_centers,
                            $household->total_vol_aggregation_center_sales,
                            $submittedBy

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmp':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [

                    'Actor ID',
                    'EPA',
                    'Section',
                    'District',
                    'Enterprise',
                    'Date of Recruitment',
                    'Name of Actor',
                    'Name of Representative',
                    'Phone Number',
                    'Type',
                    'Approach',
                    'Sector',
                    'Members (Female 18-35)',
                    'Members (Male 18-35)',
                    'Members (Male 35+)',
                    'Members (Female 35+)',
                    'Group',
                    'Establishment Status',
                    'Is Registered',
                    'Registration Body',
                    'Registration Number',
                    'Registration Date',
                    'Employees (Formal Female 18-35)',
                    'Employees (Formal Male 18-35)',
                    'Employees (Formal Male 35+)',
                    'Employees (Formal Female 35+)',
                    'Employees (Informal Female 18-35)',
                    'Employees (Informal Male 18-35)',
                    'Employees (Informal Male 35+)',
                    'Employees (Informal Female 35+)',

                    'Market Segment (Fresh)',
                    'Market Segment (Processed)',
                    'Has RTC Market Contract',
                    'Total Volume Production (Previous Season)',
                    'Production Value (Previous Season Total)',
                    'Production Value (Date of Max Sales)',
                    'Production Value (USD Rate)',
                    'Production Value (USD Value)',

                    'Sells to Domestic Markets',
                    'Sells to International Markets',
                    'Uses Market Information Systems',
                    'Sells to Aggregation Centers',
                    'Total Volume Aggregation Center Sales',
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RtcProductionProcessor::chunk(2000, function ($households) use ($writer) {
                    foreach ($households as $household) {
                        $submittedBy = '';
                        $user = User::find($household->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }

                        $writer->addRow([

                            $household->pp_id,
                            $household->epa,
                            $household->section,
                            $household->district,
                            $household->enterprise,
                            $household->date_of_recruitment,
                            $household->name_of_actor,
                            $household->name_of_representative,
                            $household->phone_number,
                            $household->type,
                            $household->approach,
                            $household->sector,
                            $household->mem_female_18_35,
                            $household->mem_male_18_35,
                            $household->mem_male_35_plus,
                            $household->mem_female_35_plus,
                            $household->group,
                            $household->establishment_status,
                            $household->is_registered,
                            $household->registration_body,
                            $household->registration_number,
                            $household->registration_date,
                            $household->emp_formal_female_18_35,
                            $household->emp_formal_male_18_35,
                            $household->emp_formal_male_35_plus,
                            $household->emp_formal_female_35_plus,
                            $household->emp_informal_female_18_35,
                            $household->emp_informal_male_18_35,
                            $household->emp_informal_male_35_plus,
                            $household->emp_informal_female_35_plus,
                            $household->market_segment_fresh,
                            $household->market_segment_processed,
                            $household->has_rtc_market_contract,
                            $household->total_vol_production_previous_season,
                            $household->prod_value_previous_season_total,
                            $household->prod_value_previous_season_date_of_max_sales,
                            $household->prod_value_previous_season_usd_rate,
                            $household->prod_value_previous_season_usd_value,

                            $household->sells_to_domestic_markets,
                            $household->sells_to_international_markets,
                            $household->uses_market_information_systems,
                            $household->sells_to_aggregation_centers,
                            $household->total_vol_aggregation_center_sales,
                            $submittedBy

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'rpmfFU':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'ACTOR ID',
                    'Date of Follow-up',
                    'Number of Plantlets Produced (Cassava)',
                    'Number of Plantlets Produced (Potato)',
                    'Number of Plantlets Produced (Sweet Potato)',
                    'Number of Screen House Vines Harvested',
                    'Number of Screen House Mini Tubers Harvested',
                    'Number of SAH Plants Produced',
                    'Is Registered Seed Producer',
                    'Registration Number (Seed Producer)',
                    'Registration Date (Seed Producer)',
                    'Uses Certified Seed',
                    'Market Segment (Fresh)',
                    'Market Segment (Processed)',
                    'Has RTC Market Contract',
                    'Total Volume of Production (Previous Season)',
                    'Production Value (Previous Season - Total)',
                    'Date of Maximum Sales (Previous Season)',
                    'USD Rate (Previous Season)',
                    'USD Value (Previous Season)',
                    'Total Volume of Irrigation Production (Previous Season)',
                    'Irrigation Production Value (Previous Season - Total)',
                    'Date of Maximum Sales (Irrigation Production)',
                    'USD Rate (Irrigation Production)',
                    'USD Value (Irrigation Production)',
                    'Sells to Domestic Markets',
                    'Sells to International Markets',
                    'Uses Market Information Systems',
                    'Sells to Aggregation Centers',
                    'Total Volume of Aggregation Center Sales',
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerFollowUp::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $followUp) {
                        $submittedBy = '';
                        $user = User::find($followUp->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }

                        $writer->addRow([

                            $followUp->farmers->pf_id,
                            $followUp->date_of_follow_up,
                            $followUp->number_of_plantlets_produced_cassava,
                            $followUp->number_of_plantlets_produced_potato,
                            $followUp->number_of_plantlets_produced_sweet_potato,
                            $followUp->number_of_screen_house_vines_harvested,
                            $followUp->number_of_screen_house_min_tubers_harvested,
                            $followUp->number_of_sah_plants_produced,
                            $followUp->is_registered_seed_producer,
                            $followUp->registration_number_seed_producer,
                            $followUp->registration_date_seed_producer,
                            $followUp->uses_certified_seed,
                            $followUp->market_segment_fresh,
                            $followUp->market_segment_processed,
                            $followUp->has_rtc_market_contract,
                            $followUp->total_vol_production_previous_season,
                            $followUp->prod_value_previous_season_total,
                            $followUp->prod_value_previous_season_date_of_max_sales,
                            $followUp->prod_value_previous_season_usd_rate,
                            $followUp->prod_value_previous_season_usd_value,
                            $followUp->total_vol_irrigation_production_previous_season,
                            $followUp->irr_prod_value_previous_season_total,
                            $followUp->irr_prod_value_previous_season_date_of_max_sales,
                            $followUp->irr_prod_value_previous_season_usd_rate,
                            $followUp->irr_prod_value_previous_season_usd_value,
                            $followUp->sells_to_domestic_markets,
                            $followUp->sells_to_international_markets,
                            $followUp->uses_market_information_systems,
                            $followUp->sells_to_aggregation_centers,
                            $followUp->total_vol_aggregation_center_sales,
                            $submittedBy
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmpFU':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'ACTOR ID',
                    'Date of Follow-up',

                    'Market Segment (Fresh)',
                    'Market Segment (Processed)',
                    'Has RTC Market Contract',
                    'Total Volume of Production (Previous Season)',
                    'Production Value (Previous Season - Total)',
                    'Date of Maximum Sales (Previous Season)',
                    'USD Rate (Previous Season)',
                    'USD Value (Previous Season)',

                    'Sells to Domestic Markets',
                    'Sells to International Markets',
                    'Uses Market Information Systems',
                    'Sells to Aggregation Centers',
                    'Total Volume of Aggregation Center Sales',
                    'Submitted by'
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorFollowUp::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $followUp) {
                        $submittedBy = '';
                        $user = User::find($followUp->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }
                        $writer->addRow([

                            $followUp->processors->pp_id,
                            $followUp->date_of_follow_up,

                            $followUp->market_segment_fresh,
                            $followUp->market_segment_processed,
                            $followUp->has_rtc_market_contract,
                            $followUp->total_vol_production_previous_season,
                            $followUp->prod_value_previous_season_total,
                            $followUp->prod_value_previous_season_date_of_max_sales,
                            $followUp->prod_value_previous_season_usd_rate,
                            $followUp->prod_value_previous_season_usd_value,

                            $followUp->sells_to_domestic_markets,
                            $followUp->sells_to_international_markets,
                            $followUp->uses_market_information_systems,
                            $followUp->sells_to_aggregation_centers,
                            $followUp->total_vol_aggregation_center_sales,
                            $submittedBy
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'rpmfCA':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',

                    'Date Recorded',
                    'Partner Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales'

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerConcAgreement::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $household) {
                        $writer->addRow([

                            $household->farmers->pf_id,

                            $household->date_recorded,
                            $household->partner_name,
                            $household->country,
                            $household->date_of_maximum_sale,
                            $household->product_type,
                            $household->volume_sold_previous_period,
                            $household->financial_value_of_sales,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmpCA':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',

                    'Date Recorded',
                    'Partner Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales'

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorConcAgreement::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $household) {
                        $writer->addRow([

                            $household->processors->pp_id,

                            $household->date_recorded,
                            $household->partner_name,
                            $household->country,
                            $household->date_of_maximum_sale,
                            $household->product_type,
                            $household->volume_sold_previous_period,
                            $household->financial_value_of_sales,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'rpmfDM':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Date Recorded",
                    "Crop Type",
                    "Market Name",
                    "District",
                    "Date of Maximum Sale",
                    "Product Type",
                    "Volume Sold Previous Period",
                    "Financial Value of Sales",


                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerDomMarket::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->date_recorded,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->district,
                            $farmer->date_of_maximum_sale,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmpDM':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Date Recorded",
                    "Crop Type",
                    "Market Name",
                    "District",
                    "Date of Maximum Sale",
                    "Product Type",
                    "Volume Sold Previous Period",
                    "Financial Value of Sales",


                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorDomMarket::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->processors->pp_id,
                            $farmer->date_recorded,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->district,
                            $farmer->date_of_maximum_sale,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;


            case 'rpmfIM':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Date Recorded",
                    "Crop Type",
                    "Market Name",
                    "Country",
                    "Date of Maximum Sale",
                    "Product Type",
                    "Volume Sold Previous Period",
                    "Financial Value of Sales",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerInterMarket::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->date_recorded,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->country,  // Assuming 'country' exists on the model
                            $farmer->date_of_maximum_sale,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;


            case 'rpmpIM':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Date Recorded",
                    "Crop Type",
                    "Market Name",
                    "Country",
                    "Date of Maximum Sale",
                    "Product Type",
                    "Volume Sold Previous Period",
                    "Financial Value of Sales",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorInterMarket::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->processors->pp_id,
                            $farmer->date_recorded,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->country,  // Assuming 'country' exists on the model
                            $farmer->date_of_maximum_sale,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmfAC':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Name of Aggregation Center",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerAggregationCenter::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->name,


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;


            case 'rpmpAC':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Name of Aggregation Center",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorAggregationCenter::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->processors->pp_id,
                            $farmer->name,


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmfMIS':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Name of Market Information Systems",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerAggregationCenter::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->name,


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmpMIS':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Name of Market Information Systems",

                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmProcessorAggregationCenter::with('processors')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->processors->pp_id,
                            $farmer->name,


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'rpmfBS':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Variety",
                    "Area",
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerBasicSeed::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area



                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmfCS':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Variety",
                    "Area",
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerCertifiedSeed::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area



                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmfFC':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Actor ID',
                    "Variety",
                    "Area",
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                RpmFarmerAreaCultivation::with('farmers')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([

                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'src':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'School ID',
                    'School Name',
                    'District',
                    'EPA',
                    'Section',
                    'Crop',
                    'Male Count',
                    'Female Count',
                    'Total',
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                SchoolRtcConsumption::with('user')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $item) {

                        $submittedBy = '';
                        $user = User::find($item->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }
                        $writer->addRow([

                            $item->sc_id,
                            $item->school_name ?? null,
                            $item->district ?? null,
                            $item->epa ?? null,
                            $item->section ?? null,
                            $item->crop,
                            $item->male_count,
                            $item->female_count,
                            $item->total,
                            $submittedBy,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'att':

                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    "Meeting ID",
                    "Meeting Title",
                    "Meeting Category",
                    "RTC Crop (Cassava)",
                    "RTC Crop (Potato)",
                    "RTC Crop (Sweet Potato)",
                    "Venue",
                    "District",
                    "Start Date",
                    "End Date",
                    "Total Days",
                    "Name",
                    "Sex",
                    "Organization",
                    "Designation",
                    "Phone Number",
                    "Email",
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);

                // Process data in chunks
                AttendanceRegister::with('user')->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $record) {

                        $submittedBy = '';
                        $user = User::find($record->user_id); {
                            $organisation = $user->organisation->name;
                            $name = $user->name;

                            $submittedBy = $name . " (" . $organisation . ")";
                        }
                        $writer->addRow([

                            $record->att_id,
                            $record->meetingTitle,
                            $record->meetingCategory,
                            $record->rtcCrop_cassava,
                            $record->rtcCrop_potato,
                            $record->rtcCrop_sweet_potato,
                            $record->venue,
                            $record->district,
                            $record->startDate,
                            $record->endDate,
                            $record->totalDays,
                            $record->name,
                            $record->sex,
                            $record->organization,
                            $record->designation,
                            $record->phone_number,
                            $record->email,
                            $submittedBy,

                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            default:
                $this->fail('Invalid model name!');
                return;
                break;
        }


        function chunkRecords($model)
        {
            $totalCount = $model::count();
            $chunkSize = 100;
            // Set chunk size dynamically depending on the total number of records
            if ($totalCount > 10000) {
                $chunkSize = 2000; // Larger chunks for large datasets
            } elseif ($totalCount > 5000) {
                $chunkSize = 1000;
            } else {
                $chunkSize = 500;  // Smaller chunks for smaller datasets
            }

            return $chunkSize;

        }
    }
}
