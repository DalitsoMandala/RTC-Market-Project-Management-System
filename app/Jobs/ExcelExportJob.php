<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Recruitment;
use App\Models\Organisation;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\RtcConsumption;
use App\Traits\FormEssentials;
use App\Models\ReportingPeriod;
use App\Models\SeedBeneficiary;
use App\Models\SystemReportData;
use App\Models\AttendanceRegister;
use App\Models\FarmerSeedRegistration;
use App\Models\MarketData;
use App\Models\RpmFarmerBasicSeed;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmFarmerCertifiedSeed;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RtcProductionProcessor;
use Illuminate\Queue\SerializesModels;
use App\Models\RecruitSeedRegistration;
use App\Models\RpmProcessorInterMarket;
use Illuminate\Support\Facades\Storage;
use App\Models\RpmFarmerAreaCultivation;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RpmProcessorConcAgreement;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\RpmFarmerAggregationCenter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\RpmProcessorAggregationCenter;
use App\Models\RpmFarmerMarketInformationSystem;
use App\Models\RpmProcessorMarketInformationSystem;
// Use Cache for progress tracking

class ExcelExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    use FormEssentials;
    public $name;
    public $uniqueID;
    public $progressKey;
    public $statusKey;
    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($name, $uniqueID, $user = null)
    {
        $this->name     = $name;
        $this->uniqueID = $uniqueID;
        $this->user     = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $directory = 'public/exports';
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        switch ($this->name) {

            case 'rpmf':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $columns = [
                    "ID" => "rn",
                    "Farmer ID" => "pf_id",
                    "Group name" => "group_name",
                    "Date of follow up" => "date_of_followup",
                    "Enterprise" => "enterprise",
                    "District" => "district",
                    "EPA" => "epa",
                    "Section" => "section",
                    "Number of plantlets produced/cassava" => "number_of_plantlets_produced->cassava",
                    "Number of plantlets produced/potato" => "number_of_plantlets_produced->potato",
                    "Number of plantlets produced/sweet potato" => "number_of_plantlets_produced->sweet_potato",
                    "Number of screen house vines harvested" => "number_of_screen_house_vines_harvested",
                    "Number of screen house min tubers harvested" => "number_of_screen_house_min_tubers_harvested",
                    "Number of sah plants produced" => "number_of_sah_plants_produced",
                    "Is registered seed producer" => "is_registered_seed_producer",
                    "Uses certified seed" => "uses_certified_seed",
                    "Market segment (Fresh)" => "market_segment_fresh",
                    "Market segment (Processed)" => "market_segment_processed",
                    "Market segment (Cuttings)" => "market_segment_cuttings",
                    "Has rtc market contract" => "has_rtc_market_contract",
                    "Volume of production (Produce)" => "total_vol_production_previous_season_produce",
                    "Volume of production (Seed)" => "total_vol_production_previous_season_seed",
                    "Volume of production (Cuttings)" => "total_vol_production_previous_season_cuttings",
                    "Volume of production Seed Bundle" => "total_vol_production_previous_season_seed_bundle",
                    "Total volume of production (Metric Tonnes)" => "total_vol_production_previous_season",
                    "Value of Production (Produce)" => "prod_value_previous_season_produce",
                    "Value of Production (Produce Prevailing Price)" => "prod_value_produce_prevailing_price",
                    "Value of Production (Seed)" => "prod_value_previous_season_seed",
                    "Value of Production (Seed Prevailing Price)" => "prod_value_seed_prevailing_price",
                    "Value of Production (Cuttings)" => "prod_value_previous_season_cuttings",
                    "Value of Production (Cuttings Prevailing Price)" => "prod_value_cuttings_prevailing_price",
                    "Value of Production Seed Bundle" => "prod_value_previous_season_seed_bundle",
                    "Total Value of Production (Metric Tonnes)" => "prod_value_previous_season_total",
                    "Total Value of Production (USD Rate)" => "prod_value_previous_season_usd_rate",
                    "Total Value of Production (USD)" => "prod_value_previous_season_usd_value",
                    "Volume of irrigation production (Produce)" => "total_vol_irrigation_production_previous_season_produce",
                    "Volume of irrigation production (Seed)" => "total_vol_irrigation_production_previous_season_seed",
                    "Volume of irrigation production (Cuttings)" => "total_vol_irrigation_production_previous_season_cuttings",
                    "Volume of irrigation production Seed Bundle" => "total_vol_irrigation_production_previous_season_seed_bundle",
                    "Total volume of irrigation production (Metric Tonnes)" => "total_vol_irrigation_production_previous_season",
                    "Value of irrigation Production (Produce)" => "irr_prod_value_previous_season_produce",
                    "Value of irrigation Production (Produce Prevailing Price)" => "irr_prod_value_produce_prevailing_price",
                    "Value of irrigation Production (Seed)" => "irr_prod_value_previous_season_seed",
                    "Value of irrigation Production (Seed Prevailing Price)" => "irr_prod_value_seed_prevailing_price",
                    "Value of irrigation Production (Cuttings)" => "irr_prod_value_previous_season_cuttings",
                    "Value of irrigation Production (Cuttings Prevailing Price)" => "irr_prod_value_cuttings_prevailing_price",
                    "Value of irrigation Production Seed Bundle" => "irr_prod_value_previous_season_seed_bundle",
                    "Total Value of irrigation Production (Metric Tonnes)" => "irr_prod_value_previous_season_total",
                    "Total Value of irrigation Production (USD Rate)" => "irr_prod_value_previous_season_usd_rate",
                    "Total Value of irrigation Production (USD)" => "irr_prod_value_previous_season_usd_value",
                    "Sells to domestic markets" => "sells_to_domestic_markets",
                    "Sells to international markets" => "sells_to_international_markets",
                    "Uses market information systems" => "uses_market_information_systems",
                    "Sells to aggregation centers" => "sells_to_aggregation_centers",
                    "Total volume of aggregation center sales" => "total_vol_aggregation_center_sales",
                    "Submitted by" => "submittedBy"
                ];

                $headers = array_keys($columns);

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('RTC Production Farmers');
                $query = RtcProductionFarmer::query()->select([
                    'rtc_production_farmers.*',
                    DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
                ]);
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->where('organisation_id', $organisation->id);
                }

                // Process data in chunks
                $query->chunk(2000, function ($items) use ($writer) {
                    foreach ($items as $item) {
                        $submittedBy = '';
                        $user        = User::find($item->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }



                        $writer->addRow([
                            $item->rn,
                            $item->pf_id,
                            $item->group_name,
                            $item->date_of_followup ? Carbon::parse($item->date_of_followup)->format('d/m/Y') : null,
                            $item->enterprise,
                            $item->district,
                            $item->epa,
                            $item->section,
                            $item->number_of_plantlets_produced_cassava,
                            $item->number_of_plantlets_produced_potato,
                            $item->number_of_plantlets_produced_sweet_potato,
                            $item->number_of_screen_house_vines_harvested,
                            $item->number_of_screen_house_min_tubers_harvested,
                            $item->number_of_sah_plants_produced,
                            $item->is_registered_seed_producer,
                            $item->uses_certified_seed,
                            $item->market_segment_fresh,
                            $item->market_segment_processed,
                            $item->market_segment_cuttings,
                            $item->has_rtc_market_contract,
                            $item->total_vol_production_previous_season_produce,
                            $item->total_vol_production_previous_season_seed,
                            $item->total_vol_production_previous_season_cuttings,
                            $item->total_vol_production_previous_season_seed_bundle,
                            $item->total_vol_production_previous_season,
                            $item->prod_value_previous_season_produce,
                            $item->prod_value_produce_prevailing_price,
                            $item->prod_value_previous_season_seed,
                            $item->prod_value_seed_prevailing_price,
                            $item->prod_value_previous_season_cuttings,
                            $item->prod_value_cuttings_prevailing_price,
                            $item->prod_value_previous_season_seed_bundle,
                            $item->prod_value_previous_season_total,
                            $item->prod_value_previous_season_usd_rate,
                            $item->prod_value_previous_season_usd_value,
                            $item->total_vol_irrigation_production_previous_season_produce,
                            $item->total_vol_irrigation_production_previous_season_seed,
                            $item->total_vol_irrigation_production_previous_season_cuttings,
                            $item->total_vol_irrigation_production_previous_season_seed_bundle,
                            $item->total_vol_irrigation_production_previous_season,
                            $item->irr_prod_value_previous_season_produce,
                            $item->irr_prod_value_produce_prevailing_price,
                            $item->irr_prod_value_previous_season_seed,
                            $item->irr_prod_value_seed_prevailing_price,
                            $item->irr_prod_value_previous_season_cuttings,
                            $item->irr_prod_value_cuttings_prevailing_price,
                            $item->irr_prod_value_previous_season_seed_bundle,
                            $item->irr_prod_value_previous_season_total,
                            $item->irr_prod_value_previous_season_usd_rate,
                            $item->irr_prod_value_previous_season_usd_value,
                            $item->sells_to_domestic_markets,
                            $item->sells_to_international_markets,
                            $item->uses_market_information_systems,
                            $item->sells_to_aggregation_centers,
                            $item->total_vol_aggregation_center_sales,
                            $submittedBy
                        ]);
                    }
                });
                // add new sheet
                $writer->addNewSheetAndMakeItCurrent('Contractual Aggrement');
                $headers = [

                    'Farmer ID',
                    'Date Recorded',
                    'Partner Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];

                $writer->addHeader($headers);
                $query  = RpmFarmerConcAgreement::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {
                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $item) {
                        $writer->addRow([
                            $item->farmers->pf_id,
                            $item->date_recorded ? Carbon::parse($item->date_recorded)->format('d/m/Y') : null,
                            $item->partner_name,
                            $item->country,
                            $item->date_of_maximum_sale ? Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y') : null,
                            $item->product_type,
                            $item->volume_sold_previous_period,
                            $item->financial_value_of_sales,
                        ]);
                    }
                });
                // add new sheet
                $writer->addNewSheetAndMakeItCurrent('Domestic Markets');
                $headers = [
                    'Farmer ID',
                    'Date Recorded',
                    'Crop Type',
                    'Market Name',
                    'District',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];
                $writer->addHeader($headers);
                $query  = RpmFarmerDomMarket::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->date_recorded ?  Carbon::parse($farmer->date_recorded)->format('d/m/Y') : null,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->district,
                            $farmer->date_of_maximum_sale ? Carbon::parse($farmer->date_of_maximum_sale)->format('d/m/Y') : null,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });


                $writer->addNewSheetAndMakeItCurrent('International Markets');
                $headers = [
                    'Farmer ID',
                    'Date Recorded',
                    'Crop Type',
                    'Market Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmFarmerInterMarket::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->date_recorded ? Carbon::parse($farmer->date_recorded)->format('d/m/Y') : null,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->country, // Assuming 'country' exists on the model
                            $farmer->date_of_maximum_sale ? Carbon::parse($farmer->date_of_maximum_sale)->format('d/m/Y') : null,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Aggregation Centers');
                $headers = [
                    'Farmer ID',
                    'Name of Aggregation Center',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmFarmerAggregationCenter::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->name,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Market Information System');
                $headers = [
                    'Farmer ID',
                    'Name of Market Information System',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmFarmerMarketInformationSystem::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {

                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($items) use ($writer) {
                    foreach ($items as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->name,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Basic Seed Multiplication');
                $headers = [
                    'Farmer ID',
                    'Variety',
                    'Area',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);
                $query  = RpmFarmerBasicSeed::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Certified Seed Multiplication');
                $headers = [
                    'Farmer ID',
                    'Variety',
                    'Area',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);
                $query  = RpmFarmerCertifiedSeed::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Area Under Cultivation');
                $headers = [
                    'Farmer ID',
                    'Variety',
                    'Area',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);
                $query  = RpmFarmerAreaCultivation::query()->with('farmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('farmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->area,
                        ]);
                    }
                });


                $writer->addNewSheetAndMakeItCurrent('Seed services Unit');
                $headers = [
                    'Farmer ID',
                    'Variety',
                    'Registration Date',
                    'Registration Number',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);
                $query  = FarmerSeedRegistration::query()->with('productionFarmers');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('productionFarmers', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->farmers->pf_id,
                            $farmer->variety,
                            $farmer->reg_date,
                            $farmer->reg_no,
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rtcConsumption':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'ID',
                    'ENTITY ID',
                    'EPA',
                    'Section',
                    'District',
                    'Entity Name',
                    'Entity Type',
                    'Date',
                    'Cassava Crop',
                    'Potato Crop',
                    'Sweet Potato Crop',
                    'Male Count',
                    'Female Count',
                    'Total',
                    'Number of Households',
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('RTC Consumption');
                $query = RtcConsumption::with('user')->select([
                    'rtc_consumptions.*',
                    DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
                ]);
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->where('organisation_id', $organisation->id);
                }

                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $item) {
                        $submittedBy = '';
                        $user        = User::find($item->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }
                        $writer->addRow([
                            $item->rn,
                            $item->en_id,
                            $item->epa,
                            $item->section,
                            $item->district,
                            $item->entity_name,
                            $item->entity_type,
                            $item->date,
                            $item->crop_cassava,
                            $item->crop_potato,
                            $item->crop_sweet_potato,
                            $item->male_count,
                            $item->female_count,
                            $item->total,
                            $item->number_of_households,
                            $submittedBy,
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'rpmp':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    "ID" => "rn",
                    "Processor ID" => "pp_id",
                    "Date of follow up" => "date_of_followup_formatted",
                    "Enterprise" => "enterprise",
                    "District" => "district",
                    "EPA" => "epa",
                    "Section" => "section",
                    "Market segment (Fresh)" => "market_segment_fresh",
                    "Market segment (Processed)" => "market_segment_processed",
                    "Has RTC Contractual Agreement" => "has_rtc_market_contract",
                    "Volume of production (Produce)" => "total_vol_production_previous_season_produce",
                    "Volume of production (Seed)" => "total_vol_production_previous_season_seed",
                    "Volume of production (Cuttings)" => "total_vol_production_previous_season_cuttings",
                    "Volume of production Seed Bundle" => "total_vol_production_previous_season_seed_bundle",
                    "Total volume of production (Metric Tonnes)" => "total_vol_production_previous_season",
                    "Value of Production (Produce)" => "prod_value_previous_season_produce",
                    "Value of Production (Produce Prevailing Price)" => "prod_value_produce_prevailing_price",
                    "Value of Production (Seed)" => "prod_value_previous_season_seed",
                    "Value of Production (Seed Prevailing Price)" => "prod_value_seed_prevailing_price",
                    "Value of Production (Cuttings)" => "prod_value_previous_season_cuttings",
                    "Value of Production (Cuttings Prevailing Price)" => "prod_value_cuttings_prevailing_price",
                    "Value of Production Seed Bundle" => "prod_value_previous_season_seed_bundle",
                    "Total Value of Production (Metric Tonnes)" => "prod_value_previous_season_total",
                    "Total Value of Production (USD Rate)" => "prod_value_previous_season_usd_rate",
                    "Total Value of Production (USD)" => "prod_value_previous_season_usd_value",
                    "Sells to domestic markets" => "sells_to_domestic_markets",
                    "Sells to international markets" => "sells_to_international_markets",
                    "Uses market information systems" => "uses_market_information_systems",
                    "Sells to aggregation centers" => "sells_to_aggregation_centers",
                    "Total Volume of Aggregation Center Sales" => "total_vol_aggregation_center_sales",
                    "Submitted by" => "submitted_by"
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader(array_keys($headers));

                $writer->nameCurrentSheet('RTC Production Processors');
                $query  = RtcProductionProcessor::query();
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->where('organisation_id', $organisation->id);
                }

                // Process data in chunks
                $query->chunk(2000, function ($items) use ($writer) {
                    foreach ($items as $item) {
                        $submittedBy = '';
                        $user        = User::find($item->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }

                        $writer->addRow([
                            $item->rn,
                            $item->pp_id,
                            $item->date_of_followup ? Carbon::parse($item->date_of_followup)->format('d-m-Y') : '',
                            $item->enterprise,
                            $item->district,
                            $item->epa,
                            $item->section,
                            $item->market_segment_fresh,
                            $item->market_segment_processed,
                            $item->has_rtc_market_contract,
                            $item->total_vol_production_previous_season_produce,
                            $item->total_vol_production_previous_season_seed,
                            $item->total_vol_production_previous_season_cuttings,
                            $item->total_vol_production_previous_season_seed_bundle,
                            $item->total_vol_production_previous_season,
                            $item->prod_value_previous_season_produce,
                            $item->prod_value_produce_prevailing_price,
                            $item->prod_value_previous_season_seed,
                            $item->prod_value_seed_prevailing_price,
                            $item->prod_value_previous_season_cuttings,
                            $item->prod_value_cuttings_prevailing_price,
                            $item->prod_value_previous_season_seed_bundle,
                            $item->prod_value_previous_season_total,
                            $item->prod_value_previous_season_usd_rate,
                            $item->prod_value_previous_season_usd_value,
                            $item->sells_to_domestic_markets,
                            $item->sells_to_international_markets,
                            $item->uses_market_information_systems,
                            $item->sells_to_aggregation_centers,
                            $item->total_vol_aggregation_center_sales,
                            $submittedBy,
                        ]);
                    }
                });




                // add new sheet
                $writer->addNewSheetAndMakeItCurrent('Contractual Aggrement');
                $headers = [

                    'Processor ID',
                    'Date Recorded',
                    'Partner Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];

                $writer->addHeader($headers);
                $query  = RpmProcessorConcAgreement::query()->with('processors');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('processors', function ($model) use ($organisation) {
                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $item) {
                        $writer->addRow([
                            $item->processors->pp_id,
                            $item->date_recorded ? Carbon::parse($item->date_recorded)->format('d/m/Y') : null,
                            $item->partner_name,
                            $item->country,
                            $item->date_of_maximum_sale ? Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y') : null,
                            $item->product_type,
                            $item->volume_sold_previous_period,
                            $item->financial_value_of_sales,
                        ]);
                    }
                });
                // add new sheet
                $writer->addNewSheetAndMakeItCurrent('Domestic Markets');
                $headers = [
                    'Processor ID',
                    'Date Recorded',
                    'Crop Type',
                    'Market Name',
                    'District',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];
                $writer->addHeader($headers);
                $query  = RpmProcessorDomMarket::query()->with('processors');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('processors', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->processors->pp_id,
                            $farmer->date_recorded ?  Carbon::parse($farmer->date_recorded)->format('d/m/Y') : null,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->district,
                            $farmer->date_of_maximum_sale ? Carbon::parse($farmer->date_of_maximum_sale)->format('d/m/Y') : null,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });


                $writer->addNewSheetAndMakeItCurrent('International Markets');
                $headers = [
                    'Processor ID',
                    'Date Recorded',
                    'Crop Type',
                    'Market Name',
                    'Country',
                    'Date of Maximum Sale',
                    'Product Type',
                    'Volume Sold Previous Period',
                    'Financial Value of Sales',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmProcessorInterMarket::query()->with('processors');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('processors', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->processors->pp_id,
                            $farmer->date_recorded ? Carbon::parse($farmer->date_recorded)->format('d/m/Y') : null,
                            $farmer->crop_type,
                            $farmer->market_name,
                            $farmer->country, // Assuming 'country' exists on the model
                            $farmer->date_of_maximum_sale ? Carbon::parse($farmer->date_of_maximum_sale)->format('d/m/Y') : null,
                            $farmer->product_type,
                            $farmer->volume_sold_previous_period,
                            $farmer->financial_value_of_sales,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Aggregation Centers');
                $headers = [
                    'Processor ID',
                    'Name of Aggregation Center',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmProcessorAggregationCenter::query()->with('processors');
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('processors', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $farmer) {
                        $writer->addRow([
                            $farmer->processors->pp_id,
                            $farmer->name,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Market Information System');
                $headers = [
                    'Processor ID',
                    'Name of Market Information System',
                ];

                // Create a new SimpleExcelWriter instance
                $writer->addHeader($headers);

                $query = RpmProcessorMarketInformationSystem::query()->with('processors');
                if ($this->user && $this->user->hasAnyRole('external')) {

                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->whereHas('processors', function ($model) use ($organisation) {

                        $model->where('organisation_id', $organisation->id);
                    });
                }
                // Process data in chunks
                $query->chunk(2000, function ($items) use ($writer) {
                    foreach ($items as $farmer) {
                        $writer->addRow([
                            $farmer->processors->pp_id,
                            $farmer->name,
                        ]);
                    }
                });




                $writer->close(); // Finalize the file

                break;







            case 'att':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'ID',
                    'Attendee ID',
                    'Name',
                    'Sex',
                    'Meeting Title',
                    'Meeting Category',
                    'RTC Crop (Cassava)',
                    'RTC Crop (Potato)',
                    'RTC Crop (Sweet Potato)',
                    'Venue',
                    'District',
                    'Start Date',
                    'End Date',
                    'Total Days',


                    'Organization',
                    'Designation',
                    'Phone Number',
                    'Email',
                    'Submitted by',
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('Attendances');
                $query = AttendanceRegister::query()->with('user')->select([
                    'attendance_registers.*',
                    DB::raw(' ROW_NUMBER() OVER (ORDER BY id) AS rn')
                ]);
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $user         = $this->user;
                    $organisation = User::find($user->id)->organisation;
                    $query->where('organisation_id', $organisation->id);
                }
                // Process data in chunks
                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $record) {
                        $submittedBy = '';
                        $user        = User::find($record->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }
                        $writer->addRow([
                            $record->rn,
                            $record->att_id,
                            $record->name,
                            $record->sex,
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

            case 'report':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headers = [
                    'Disaggregation',
                    'Value',
                    'Indicator Name',
                    'Indicator #',
                    'Project',
                    'Reporting period',
                    'Reporting period time',
                    'Organisation',
                    'Project year',
                    'Start Date',
                    'End Date',
                    'Enterprise'
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('Reports');
                $query =  SystemReportData::query()
                    ->with('systemReport')
                    ->join('system_reports', function ($join) {
                        $join->on('system_reports.id', '=', 'system_report_data.system_report_id');
                    })
                    ->leftJoin('indicators', 'indicators.id', '=', 'system_reports.indicator_id')
                    ->leftJoin('organisations', 'organisations.id', '=', 'system_reports.organisation_id')
                    ->leftJoin('financial_years', 'financial_years.id', '=', 'system_reports.financial_year_id')
                    ->select([
                        'system_report_data.*',
                        'indicators.indicator_name as indicator_name',
                        'indicators.indicator_no as indicator_no',
                        'organisations.name as organisation_name',
                        'financial_years.number as financial_year',
                        'financial_years.start_date as financial_year_start_date',
                        'financial_years.end_date as financial_year_end_date',
                    ]);
                if ($this->user->hasAnyRole('external')) {
                    $query->where('system_reports.organisation_id', $this->user->organisation->id)

                        ->chunk(2000, function ($reports) use ($writer) {
                            foreach ($reports as $record) {
                                // Check if systemReport and reportingPeriod are available
                                $report_period  = null;
                                $type_of_period = null;
                                if ($record->systemReport && $record->systemReport->reportingPeriod) {
                                    $start_month    = $record->systemReport->reportingPeriod->start_month;
                                    $end_month      = $record->systemReport->reportingPeriod->end_month;
                                    $report_period  = $start_month . ' - ' . $end_month;
                                    $type_of_period = $record->systemReport->reportingPeriod->type;
                                }

                                $writer->addRow([
                                    $record->name,                                // Disaggregation (from `name` field)
                                    (float) $record->value,                       // Value
                                    $record->indicator_name ?? null,              // Indicator Name
                                    $record->indicator_no ?? null,                // Indicator #
                                    $record->systemReport->project->name ?? null, // Project
                                    $report_period,                               // Reporting period (null if no systemReport or reportingPeriod)
                                    $type_of_period,
                                    $record->organisation_name ?? null,         // Organisation
                                    $record->financial_year,                    // Project year
                                    $record->financial_year_start_date ?? null, // Start year
                                    $record->financial_year_end_date ?? null,   // End Year
                                    $record->systemReport->crop ?? 'All',                      // Crop
                                ]);
                            }
                        });
                    $writer->close(); // Finalize the file
                    return;
                }
                // Process data in chunks

                $query->chunk(2000, function ($reports) use ($writer) {
                    foreach ($reports as $record) {
                        // Check if systemReport and reportingPeriod are available
                        $report_period  = null;
                        $type_of_period = null;
                        if ($record->systemReport && $record->systemReport->reportingPeriod) {
                            $start_month    = $record->systemReport->reportingPeriod->start_month;
                            $end_month      = $record->systemReport->reportingPeriod->end_month;
                            $report_period  = $start_month . ' - ' . $end_month;
                            $type_of_period = $record->systemReport->reportingPeriod->type;
                        }

                        $writer->addRow([
                            $record->name,                                // Disaggregation (from `name` field)
                            (float) $record->value,                       // Value
                            $record->indicator_name ?? null,              // Indicator Name
                            $record->indicator_no ?? null,                // Indicator #
                            $record->systemReport->project->name ?? null, // Project
                            $report_period,                               // Reporting period (null if no systemReport or reportingPeriod)
                            $type_of_period,
                            $record->organisation_name ?? null,         // Organisation
                            $record->financial_year,                    // Project year
                            $record->financial_year_start_date ?? null, // Start year
                            $record->financial_year_end_date ?? null,   // End Year
                            $record->systemReport->crop ?? 'All',                       // Crop
                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;
            case 'seedBeneficiaries':
                $filePath  = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                $cropTypes = [
                    'Potato',
                    'OFSP',
                    'Cassava',

                ];
                // Define the headers
                $OFSPHeaders = [

                    'District',
                    'EPA',
                    'Section',
                    'Name of AEDO',
                    'AEDO Phone Number',
                    'Date',
                    'Name of Recipient',
                    'Group Name',
                    'Village',
                    'Sex',
                    'Age',
                    'Marital Status',
                    'Household Head',
                    'Household Size',
                    'Children Under 5 in HH',
                    'Variety Received',
                    'Bundles Received',
                    'Phone Number',
                    'National ID',
                    'Type of Plot',
                    'Type of Actor',
                    'Season Type',
                    'Submitted By'
                ];
                $PotatoHeaders = [

                    'District',
                    'EPA',
                    'Section',
                    'Name of AEDO',
                    'AEDO Phone Number',
                    'Date',
                    'Name of Recipient',
                    'Group Name',
                    'Village',
                    'Sex',
                    'Age',
                    'Marital Status',
                    'Household Head',
                    'Household Size',
                    'Children Under 5 in HH',
                    'Variety Received',
                    'Tons_KG Received',
                    'National ID',
                    'Type of Plot',
                    'Type of Actor',
                    'Season Type',
                    'Submitted By'
                ];
                // Define crop types
                $CassavaHeaders = [

                    'District',
                    'EPA',
                    'Section',
                    'Name of AEDO',
                    'AEDO Phone Number',
                    'Date',
                    'Name of Recipient',
                    'Group Name',
                    'Village',
                    'Sex',
                    'Age',
                    'Marital Status',
                    'Household Head',
                    'Household Size',
                    'Children Under 5 in HH',
                    'Variety Received',
                    'Bundles Received',
                    'National ID',
                    'Type of Plot',
                    'Type of Actor',
                    'Season Type',
                    'Submitted By'
                ];

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath);

                foreach ($cropTypes as $index => $crop) {
                    if ($index > 0) {
                        // Create a new sheet for each crop type after the first sheet
                        $writer->addNewSheetAndMakeItCurrent();
                    }

                    // Name the current sheet with the crop type and add headers
                    if ($crop == 'OFSP') {
                        $writer->nameCurrentSheet($crop)->addHeader($OFSPHeaders);
                    } else if ($crop == 'Potato') {
                        $writer->nameCurrentSheet($crop)->addHeader($PotatoHeaders);
                    } else {
                        $writer->nameCurrentSheet($crop)->addHeader($CassavaHeaders);
                    }

                    $query =  // Process data in chunks for the current crop
                        SeedBeneficiary::with(['user', 'user.organisation'])
                        ->where('crop', $crop)
                        ->select([
                            'seed_beneficiaries.crop',
                            'seed_beneficiaries.district',
                            'seed_beneficiaries.epa',
                            'seed_beneficiaries.section',
                            'seed_beneficiaries.name_of_aedo',
                            'seed_beneficiaries.aedo_phone_number',
                            'seed_beneficiaries.date',
                            'seed_beneficiaries.name_of_recipient',
                            'seed_beneficiaries.group_name',
                            'seed_beneficiaries.village',
                            'seed_beneficiaries.sex',
                            'seed_beneficiaries.age',
                            'seed_beneficiaries.marital_status',
                            'seed_beneficiaries.hh_head',
                            'seed_beneficiaries.household_size',
                            'seed_beneficiaries.children_under_5',
                            'seed_beneficiaries.variety_received',
                            'seed_beneficiaries.bundles_received',
                            'seed_beneficiaries.phone_number',
                            'seed_beneficiaries.national_id',
                            'seed_beneficiaries.type_of_plot',
                            'seed_beneficiaries.type_of_actor',
                            'seed_beneficiaries.season_type',
                            'users.name as user_name',                 // Assuming the table name for the user model is 'users'
                            'organisations.name as organisation_name', // Assuming the table name for the organisation model is 'organisations'
                        ])
                        ->join('users', 'seed_beneficiaries.user_id', '=', 'users.id')            // Assuming the foreign key is 'user_id'
                        ->join('organisations', 'seed_beneficiaries.organisation_id', '=', 'organisations.id') // Assuming the foreign key is 'organisation_id'
                    ;

                    if ($this->user && $this->user->hasAnyRole('external')) {
                        $user         = $this->user;
                        $organisation = User::find($user->id)->organisation;
                        $query->where('organisation_id', $organisation->id);
                    }

                    $query->chunk(2000, function ($seedBeneficiaries) use ($writer) {
                        foreach ($seedBeneficiaries as $record) {




                            $submittedBy = $record->user_name . ' (' . $record->organisation_name . ')';

                            $writer->addRow([

                                $record->district,
                                $record->epa,
                                $record->section,
                                $record->name_of_aedo,
                                $record->aedo_phone_number,
                                $record->date,
                                $record->name_of_recipient,
                                $record->group_name,
                                $record->village,
                                $record->sex,
                                $record->age,
                                $record->marital_status,
                                $record->hh_head,
                                $record->household_size,
                                $record->children_under_5,
                                $record->variety_received,
                                $record->bundles_received,
                                $record->phone_number,
                                $record->national_id,
                                $record->type_of_plot,
                                $record->type_of_actor,
                                $record->season_type,
                                $submittedBy
                            ]);
                        }
                    });
                }

                $writer->close(); // Finalize the file

                break;

            case 'recruits':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headerFromExports = [
                    'ID' => 'Required, Unique,  Number',
                    'EPA'                               => 'Required, Text',
                    'Section'                           => 'Required, Text',
                    'District'                          => 'Required, Text',
                    'Enterprise'                        => 'Required, Text',
                    'Date of Recruitment'               => 'Date (dd-mm-yyyy)',
                    'Name of Actor'                     => 'Text',
                    'Name of Representative'            => 'Text',
                    'Phone Number'                      => 'Text',
                    'Type'                              => 'Text, (Choose one option)',
                    'Group'                             => 'Text, (Choose one option)',
                    'Approach'                          => 'Text, (Choose one option)',
                    'Sector'                            => 'Text, (Choose one option)',
                    'Members Female 18-35'              => 'Number (>=0)',
                    'Members Male 18-35'                => 'Number (>=0)',
                    'Members Male 35+'                  => 'Number (>=0)',
                    'Members Female 35+'                => 'Number (>=0)',
                    'Category'                          => 'Text, (Choose one option)',
                    'Establishment Status'              => 'New/Old, (Choose one option)',
                    'Is Registered'                     => 'Boolean (1/0)',
                    'Registration Body'                 => 'Text',
                    'Registration Number'               => 'Text',
                    'Registration Date'                 => 'Date (dd-mm-yyyy)',
                    'Employees Formal Female 18-35'     => 'Number (>=0)',
                    'Employees Formal Male 18-35'       => 'Number (>=0)',
                    'Employees Formal Male 35+'         => 'Number (>=0)',
                    'Employees Formal Female 35+'       => 'Number (>=0)',
                    'Employees Informal Female 18-35'   => 'Number (>=0)',
                    'Employees Informal Male 18-35'     => 'Number (>=0)',
                    'Employees Informal Male 35+'       => 'Number (>=0)',
                    'Employees Informal Female 35+'     => 'Number (>=0)',
                    'Area Under Cultivation'            => 'Number (>=0)',
                    'Is Registered Seed Producer'       => 'Boolean (1/0)',
                    'Uses Certified Seed'               => 'Boolean (1/0)',
                    'Submitted By'                      => true,
                ];
                $headers = array_keys($headerFromExports);

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('RTC Actor Recruitment');

                $query =     Recruitment::with(['user', 'user.organisation'])->select([
                    'recruitments.*',
                    DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
                ]);
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $query = $query->where('organisation_id', $this->user->organisation->id);
                }
                // Process data in chunks

                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $record) {
                        $submittedBy = '';
                        $user        = User::find($record->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }
                        $writer->addRow([
                            $record->rn,
                            $record->epa ?? 'NA',
                            $record->section ?? 'NA',
                            $record->district ?? 'NA',
                            $record->enterprise ?? 'NA',
                            $record->date_of_recruitment ?   Carbon::parse($record->date_of_recruitment)->format('d/m/Y') : 'NA',
                            $record->name_of_actor ?? 'NA',
                            $record->name_of_representative ?? 'NA',
                            $record->phone_number ?? 'NA',
                            $record->type ?? 'NA',
                            $record->group ?? 'NA',
                            $record->approach ?? 'NA',
                            $record->sector ?? 'NA',
                            $record->mem_female_18_35,
                            $record->mem_male_18_35,
                            $record->mem_male_35_plus,
                            $record->mem_female_35_plus,
                            $record->category ?? 'NA',
                            $record->establishment_status ?? 'NA',
                            $record->is_registered == 1 ? 'Yes' : 'No',
                            $record->registration_body ?? 'NA',
                            $record->registration_number ?? 'NA',
                            $record->registration_date ? Carbon::parse($record->registration_date)->format('d/m/Y') : 'NA',
                            $record->emp_formal_female_18_35,
                            $record->emp_formal_male_18_35,
                            $record->emp_formal_male_35_plus,
                            $record->emp_formal_female_35_plus,
                            $record->emp_informal_female_18_35,
                            $record->emp_informal_male_18_35,
                            $record->emp_informal_male_35_plus,
                            $record->emp_informal_female_35_plus,
                            $record->area_under_cultivation,
                            $record->is_registered_seed_producer == 1 ? 'Yes' : 'No',
                            $record->uses_certified_seed == 1 ? 'Yes' : 'No',
                            $submittedBy,
                        ]);
                    }
                });

                $writer->addNewSheetAndMakeItCurrent('Seed services unit');
                $headers = [
                    'ID' => 'Number, Exists in RTC Actor Recruitment Sheet',
                    'Registration Date' => 'Date (dd-mm-yyyy)',
                    'Registration Number' => 'Text',
                    'Variety' => 'Text',
                ];
                $writer->addHeader(array_keys($headers));
                RecruitSeedRegistration::query()->chunk(2000, function ($recruitments) use ($writer) {
                    foreach ($recruitments as $recruitment) {
                        $writer->addRow([
                            $recruitment->recruitment_id,
                            $recruitment->reg_date,
                            $recruitment->reg_no,
                            $recruitment->variety


                        ]);
                    }
                });
                $writer->close(); // Finalize the file

                break;

            case 'market_data':
                $filePath = storage_path('app/public/exports/' . $this->name . '_' . $this->uniqueID . '.xlsx');
                // Define the headers
                $headerFromExports = [
                    'ID' => 'Number, Exists in Market Data Sheet',
                    'Entry Month' => 'Date (dd-mm-yyyy)',
                    'Off-taker Name/Vehicle Reg Number' => 'Text',
                    'Trader Contact' => 'Text',
                    'Buyer Location' => 'Text',
                    'Variety Demanded' => 'Text',
                    'Quality/Size' => 'Text',
                    'Quantity' => 'Number (decimal)',
                    'Units' => 'Text',
                    'Estimated Demand (Kg)' => 'Number (decimal)',
                    'Agreed Price per Kg (MWK)' => 'Number (decimal)',
                    'Market Ordered From' => 'Text',
                    'Final Market' => 'Text',
                    'Final Market District' => 'Text',
                    'Final Market Country' => 'Text',
                    'Supply Frequency' => 'Text',
                    'Estimated Total Value (MWK)' => 'Number (decimal)',
                    'Estimated Total Value (USD)' => 'Number (decimal)',
                    'Submitted By'                      => true,
                ];
                $headers = array_keys($headerFromExports);

                // Create a new SimpleExcelWriter instance
                $writer = SimpleExcelWriter::create($filePath)->addHeader($headers);
                $writer->nameCurrentSheet('Marketing Monthly Report');

                $query =     MarketData::with(['user', 'user.organisation'])->select([
                    'marketing_data.*',
                    DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
                ]);
                if ($this->user && $this->user->hasAnyRole('external')) {
                    $query = $query->where('organisation_id', $this->user->organisation->id);
                }
                // Process data in chunks

                $query->chunk(2000, function ($followUps) use ($writer) {
                    foreach ($followUps as $record) {
                        $submittedBy = '';
                        $user        = User::find($record->user_id); {
                            $organisation = $user->organisation->name;
                            $name         = $user->name;

                            $submittedBy = $name . ' (' . $organisation . ')';
                        }
                        $writer->addRow([
                            $record->rn,
                            $record->entry_date,
                            $record->off_taker_name_vehicle_reg_number,
                            $record->trader_contact,
                            $record->buyer_location,
                            $record->variety_demanded,
                            $record->quality_size,
                            $record->quantity,
                            $record->units,
                            $record->estimated_demand_kg,
                            $record->agreed_price_per_kg,
                            $record->market_ordered_from,
                            $record->final_market,
                            $record->final_market_district,
                            $record->final_market_country,
                            $record->supply_frequency,
                            $record->estimated_total_value_mwk,
                            $record->estimated_total_value_usd,
                            $submittedBy,
                        ]);
                    }
                });


                $writer->close(); // Finalize the file
                break;
            default:
                $this->fail('Invalid model name! Naming of the excel export is unknown.');
                return;
                break;
        }
    }
}
