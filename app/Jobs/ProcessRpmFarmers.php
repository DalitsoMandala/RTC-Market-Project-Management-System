<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerDomMarket;
use App\Models\RtcProductionFarmer;
use App\Models\RpmFarmerInterMarket;
use Illuminate\Support\Facades\Cache;
use App\Models\RpmFarmerConcAgreement;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessRpmFarmers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    protected $batch_no;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batch_no = null)
    {
        $this->batch_no = $batch_no;
        Cache::put('rpmf_', []);

    }



    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put('rpmf_', [
            'rpm_farmers' => $this->mapDataFarmer(),
            'rpm_farmers_agr' => $this->mapDataFarmerAgr(),
            'rpm_farmers_flp' => $this->mapDataFarmerFlp(),
            'rpm_farmers_dom' => $this->mapDataFarmerDom(),
            'rpm_farmers_inter' => $this->mapDataFarmerInter(),
        ], now()->addMinutes(1));
    }

    public function mapDataFarmerFlp()
    {
        $results = collect();

        $batch_no = $this->batch_no;
        RpmFarmerFollowUp::with('farmers')->chunk(1000, function ($items) use (&$results) {
            foreach ($items as $item) {
                // Decode JSON fields
                $location_data = json_decode($item->location_data);
                $area_under_cultivation = json_decode($item->area_under_cultivation);
                $number_of_plantlets_produced = json_decode($item->number_of_plantlets_produced);
                $area_under_basic_seed_multiplication = json_decode($item->area_under_basic_seed_multiplication);
                $area_under_certified_seed_multiplication = json_decode($item->area_under_certified_seed_multiplication);
                $seed_service_unit_registration_details = json_decode($item->seed_service_unit_registration_details);

                // Transform and assign values
                $item->actor_name = $item->farmers->name_of_actor ?? null;


                $item->enterprise = $location_data->enterprise ?? null;
                $item->district = $location_data->district ?? null;
                $item->epa = $location_data->epa ?? null;
                $item->section = $location_data->section ?? null;
                $item->group_name = $location_data->group_name ?? null;
                $item->date_of_follow_up_formatted = Carbon::parse($item->date_of_follow_up)->format('d/m/Y');
                $item->area_under_cultivation_total = $area_under_cultivation->total ?? 0;
                $item->area_under_cultivation_variety_1 = $area_under_cultivation->variety_1 ?? null;
                $item->area_under_cultivation_variety_2 = $area_under_cultivation->variety_2 ?? null;
                $item->area_under_cultivation_variety_3 = $area_under_cultivation->variety_3 ?? null;
                $item->area_under_cultivation_variety_4 = $area_under_cultivation->variety_4 ?? null;
                $item->area_under_cultivation_variety_5 = $area_under_cultivation->variety_5 ?? null;

                $item->number_of_plantlets_produced_potato = $number_of_plantlets_produced->potato ?? null;
                $item->number_of_plantlets_produced_cassava = $number_of_plantlets_produced->cassava ?? null;
                $item->number_of_plantlets_produced_sw_potato = $number_of_plantlets_produced->sweet_potato ?? null;
                $item->number_of_screen_house_vines_harvested = $item->number_of_screen_house_vines_harvested ?? null;
                $item->number_of_screen_house_min_tubers_harvested = $item->number_of_screen_house_min_tubers_harvested ?? null;
                $item->number_of_sah_plants_produced = $item->number_of_sah_plants_produced ?? null;

                $item->basic_seed_multiplication_total = $area_under_basic_seed_multiplication->total ?? null;
                $item->basic_seed_multiplication_variety_1 = $area_under_basic_seed_multiplication->variety_1 ?? null;
                $item->basic_seed_multiplication_variety_2 = $area_under_basic_seed_multiplication->variety_2 ?? null;
                $item->basic_seed_multiplication_variety_3 = $area_under_basic_seed_multiplication->variety_3 ?? null;
                $item->basic_seed_multiplication_variety_4 = $area_under_basic_seed_multiplication->variety_4 ?? null;
                $item->basic_seed_multiplication_variety_5 = $area_under_basic_seed_multiplication->variety_5 ?? null;
                $item->basic_seed_multiplication_variety_6 = $area_under_basic_seed_multiplication->variety_6 ?? null;
                $item->basic_seed_multiplication_variety_7 = $area_under_basic_seed_multiplication->variety_7 ?? null;

                $item->area_under_certified_seed_multiplication_total = $area_under_certified_seed_multiplication->total ?? null;
                $item->area_under_certified_seed_multiplication_variety_1 = $area_under_certified_seed_multiplication->variety_1 ?? null;
                $item->area_under_certified_seed_multiplication_variety_2 = $area_under_certified_seed_multiplication->variety_2 ?? null;
                $item->area_under_certified_seed_multiplication_variety_3 = $area_under_certified_seed_multiplication->variety_3 ?? null;
                $item->area_under_certified_seed_multiplication_variety_4 = $area_under_certified_seed_multiplication->variety_4 ?? null;
                $item->area_under_certified_seed_multiplication_variety_5 = $area_under_certified_seed_multiplication->variety_5 ?? null;
                $item->area_under_certified_seed_multiplication_variety_6 = $area_under_certified_seed_multiplication->variety_6 ?? null;
                $item->area_under_certified_seed_multiplication_variety_7 = $area_under_certified_seed_multiplication->variety_7 ?? null;

                $item->is_registered_seed_producer = $item->is_registered_seed_producer == 1 ? 'Yes' : 'No';
                $item->seed_service_unit_registration_details_date = $seed_service_unit_registration_details && isset($seed_service_unit_registration_details->registration_date)
                    ? Carbon::parse($seed_service_unit_registration_details->registration_date)->format('d/m/Y')
                    : null;
                $item->seed_service_unit_registration_details_number = $seed_service_unit_registration_details->registration_number ?? null;
                $item->service_unit_date = Carbon::parse($item->service_unit_date)->format('d/m/Y');
                $item->uses_certified_seed = $item->uses_certified_seed == 1 ? 'Yes' : 'No';

                // Add the item to results
                $results->push($item);
            }

        });


        if ($this->batch_no) {
            return $results->where('uuid', $this->batch_no);
        }
        return $results;

    }

    public function mapDataFarmer()
    {
        $results = collect();

        $batch_no = $this->batch_no;
        RtcProductionFarmer::with(['user.organisation'])->chunk(1000, function ($items) use (&$results) {
            foreach ($items as $item) {
                // Decode JSON fields
                $location = json_decode($item->location_data);
                $number_of_members = json_decode($item->number_of_members);
                $registration_details = json_decode($item->registration_details);
                $area_under_cultivation = json_decode($item->area_under_cultivation);
                $number_of_plantlets_produced = json_decode($item->number_of_plantlets_produced);
                $area_under_basic_seed_multiplication = json_decode($item->area_under_basic_seed_multiplication);
                $area_under_certified_seed_multiplication = json_decode($item->area_under_certified_seed_multiplication);

                $seed_service_unit_registration_details = json_decode($item->seed_service_unit_registration_details);
                $market_segment = json_decode($item->market_segment);
                $total_production_value_previous_season = json_decode($item->total_production_value_previous_season);
                $total_irrigation_production_value_previous_season = json_decode($item->total_irrigation_production_value_previous_season);
                $aggregation_centers = json_decode($item->aggregation_centers);
                $employment_data = json_decode($item->number_of_employees);

                // Transform and assign values
                $item->date_of_recruitment = Carbon::parse($item->date_of_recruitment)->format('d/m/Y');
                $item->enterprise = $location->enterprise ?? null;
                $item->district = $location->district ?? null;
                $item->epa = $location->epa ?? null;
                $item->section = $location->section ?? null;
                $item->number_of_members_total = $number_of_members->total ?? 0;
                $item->number_of_members_female_18_35 = $number_of_members->female_18_35 ?? 0;
                $item->number_of_members_male_18_35 = $number_of_members->male_18_35 ?? 0;
                $item->number_of_members_male_35_plus = $number_of_members->male_35_plus ?? 0;
                $item->number_of_members_female_35_plus = $number_of_members->female_35_plus ?? 0;
                $item->is_registered = $item->is_registered == 1 ? 'Yes' : 'No';
                $item->registration_details_body = $registration_details->registration_body ?? null;
                $item->registration_details_date = $registration_details->registration_date ? Carbon::parse($registration_details->registration_date)->format('d/m/Y') : null;
                $item->registration_details_number = $registration_details->registration_number ?? null;

                $item->area_under_cultivation_total = $area_under_cultivation->total ?? 0;
                $item->area_under_cultivation_variety_1 = $area_under_cultivation->variety_1 ?? null;
                $item->area_under_cultivation_variety_2 = $area_under_cultivation->variety_2 ?? null;
                $item->area_under_cultivation_variety_3 = $area_under_cultivation->variety_3 ?? null;
                $item->area_under_cultivation_variety_4 = $area_under_cultivation->variety_4 ?? null;
                $item->area_under_cultivation_variety_5 = $area_under_cultivation->variety_5 ?? null;

                $item->number_of_plantlets_produced_potato = $number_of_plantlets_produced->potato ?? null;
                $item->number_of_plantlets_produced_cassava = $number_of_plantlets_produced->cassava ?? null;
                $item->number_of_plantlets_produced_sw_potato = $number_of_plantlets_produced->sweet_potato ?? null;
                $item->area_under_basic_seed_multiplication_total = $area_under_basic_seed_multiplication->total ?? null;

                $item->basic_seed_multiplication_variety_1 = $area_under_basic_seed_multiplication->variety_1 ?? null;
                $item->basic_seed_multiplication_variety_2 = $area_under_basic_seed_multiplication->variety_2 ?? null;
                $item->basic_seed_multiplication_variety_3 = $area_under_basic_seed_multiplication->variety_3 ?? null;
                $item->basic_seed_multiplication_variety_4 = $area_under_basic_seed_multiplication->variety_4 ?? null;
                $item->basic_seed_multiplication_variety_5 = $area_under_basic_seed_multiplication->variety_5 ?? null;
                $item->basic_seed_multiplication_variety_6 = $area_under_basic_seed_multiplication->variety_6 ?? null;
                $item->basic_seed_multiplication_variety_7 = $area_under_basic_seed_multiplication->variety_7 ?? null;

                $item->area_under_certified_seed_multiplication_total = $area_under_certified_seed_multiplication->total ?? null;
                $item->area_under_certified_seed_multiplication_variety_1 = $area_under_certified_seed_multiplication->variety_1 ?? null;
                $item->area_under_certified_seed_multiplication_variety_2 = $area_under_certified_seed_multiplication->variety_2 ?? null;
                $item->area_under_certified_seed_multiplication_variety_3 = $area_under_certified_seed_multiplication->variety_3 ?? null;
                $item->area_under_certified_seed_multiplication_variety_4 = $area_under_certified_seed_multiplication->variety_4 ?? null;
                $item->area_under_certified_seed_multiplication_variety_5 = $area_under_certified_seed_multiplication->variety_5 ?? null;
                $item->area_under_certified_seed_multiplication_variety_6 = $area_under_certified_seed_multiplication->variety_6 ?? null;
                $item->area_under_certified_seed_multiplication_variety_7 = $area_under_certified_seed_multiplication->variety_7 ?? null;

                $item->is_registered_seed_producer = $item->is_registered_seed_producer == 1 ? 'Yes' : 'No';
                $item->seed_service_unit_registration_details_date = $seed_service_unit_registration_details->registration_date ? Carbon::parse($seed_service_unit_registration_details->registration_date)->format('d/m/Y') : null;
                $item->seed_service_unit_registration_details_number = $seed_service_unit_registration_details->registration_number ?? null;
                $item->uses_certified_seed = $item->uses_certified_seed == 1 ? 'Yes' : 'No';
                $item->market_segment_fresh = $market_segment->fresh ?? null;
                $item->market_segment_processed = $market_segment->processed ?? null;
                $item->has_rtc_market_contract = $item->has_rtc_market_contract == 1 ? 'Yes' : 'No';
                $item->total_production_value_previous_season_date = Carbon::parse($total_production_value_previous_season->date_of_maximum_sales ?? null)->format('d/m/Y');
                $item->total_production_value_previous_season_total = $total_production_value_previous_season->total ?? null;

                $item->total_irrigation_production_value_previous_season_date = Carbon::parse($total_irrigation_production_value_previous_season->date_of_maximum_sales ?? null)->format('d/m/Y');
                $item->total_irrigation_production_value_previous_season_total = $total_irrigation_production_value_previous_season->total;

                $item->sells_to_domestic_markets = $item->sells_to_domestic_markets == 1 ? 'Yes' : 'No';
                $item->sells_to_international_markets = $item->sells_to_international_markets == 1 ? 'Yes' : 'No';
                $item->uses_market_information_systems = $item->uses_market_information_systems == 1 ? 'Yes' : 'No';
                $item->aggregation_centers_response = $aggregation_centers->response == 1 ? 'Yes' : 'No';
                $item->aggregation_centers_specify = $aggregation_centers->specify ?? null;

                // Add employment data
                $item->formal_total = $employment_data->formal->total ?? 0;
                $item->formal_male_18_35 = $employment_data->formal->male_18_35 ?? 0;
                $item->formal_female_18_35 = $employment_data->formal->female_18_35 ?? 0;
                $item->formal_male_35_plus = $employment_data->formal->male_35_plus ?? 0;
                $item->formal_female_35_plus = $employment_data->formal->female_35_plus ?? 0;
                $item->informal_total = $employment_data->informal->total ?? 0;
                $item->informal_male_18_35 = $employment_data->informal->male_18_35 ?? 0;
                $item->informal_female_18_35 = $employment_data->informal->female_18_35 ?? 0;
                $item->informal_male_35_plus = $employment_data->informal->male_35_plus ?? 0;
                $item->informal_female_35_plus = $employment_data->informal->female_35_plus ?? 0;


                // Add the item to results
                $results->push($item);
            }
        });



        if ($this->batch_no) {
            return $results->where('uuid', $this->batch_no);
        }
        return $results;
    }

    public function mapDataFarmerAgr()
    {
        $results = collect();

        $batch_no = $this->batch_no;
        RpmFarmerConcAgreement::with('farmers')->chunk(1000, function ($items) use (&$results) {


            foreach ($items as $item) {

                $item->actor_name = $item->farmers->name_of_actor ?? null;
                $item->date_recorded_formatted = Carbon::parse($item->date_recorded)->format('d/m/Y');
                $item->date_of_maximum_sale_formatted = Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y');

                $results->push($item);
            }
        });

        if ($this->batch_no) {
            return $results->where('uuid', $this->batch_no);
        }
        return $results;
    }


    public function mapDataFarmerDom()
    {
        $results = collect();

        $batch_no = $this->batch_no;
        RpmFarmerDomMarket::with('farmers')->chunk(1000, function ($items) use (&$results) {


            foreach ($items as $item) {

                $item->actor_name = $item->farmers->name_of_actor ?? null;
                $item->date_recorded_formatted = Carbon::parse($item->date_recorded)->format('d/m/Y');
                $item->date_of_maximum_sale_formatted = Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y');

                $results->push($item);
            }
        });

        if ($this->batch_no) {
            return $results->where('uuid', $this->batch_no);
        }
        return $results;
    }


    public function mapDataFarmerInter()
    {
        $results = collect();

        $batch_no = $this->batch_no;
        RpmFarmerInterMarket::with('farmers')->chunk(1000, function ($items) use (&$results) {


            foreach ($items as $item) {

                $item->actor_name = $item->farmers->name_of_actor ?? null;
                $item->date_recorded_formatted = Carbon::parse($item->date_recorded)->format('d/m/Y');
                $item->date_of_maximum_sale_formatted = Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y');

                $results->push($item);
            }
        });

        if ($this->batch_no) {
            return $results->where('uuid', $this->batch_no);
        }
        return $results;
    }
}
