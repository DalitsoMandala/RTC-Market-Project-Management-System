<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionProcessor;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class Add extends Component
{
    use LivewireAlert;

    public $location_data = [];
    public $date_of_recruitment;
    public $name_of_actor;
    public $name_of_representative;
    public $phone_number;
    public $type;
    public $approach; // For producer organizations only
    public $sector;
    public $number_of_members = []; // For producer organizations only
    public $group;
    public $establishment_status;
    public $is_registered = false;
    public $registration_details = [];
    public $number_of_employees = [];
    public $area_under_cultivation = []; // Stores area by variety (key-value pairs)
    public $number_of_plantlets_produced = [];
    public $number_of_screen_house_vines_harvested; // Sweet potatoes
    public $number_of_screen_house_min_tubers_harvested; // Potatoes
    public $number_of_sah_plants_produced; // Cassava
    public $area_under_basic_seed_multiplication = []; // Acres
    public $area_under_certified_seed_multiplication = []; // Acres
    public $is_registered_seed_producer = false;
    public $seed_service_unit_registration_details = [];
    public $uses_certified_seed = false;
    public $market_segment = []; // Multiple market segments (array of strings)
    public $has_rtc_market_contract = false;
    public $total_vol_production_previous_season;
    public $total_production_value_previous_season = [];
    public $total_vol_irrigation_production_previous_season;
    public $total_irrigation_production_value_previous_season = [];
    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;
    public $market_information_systems;
    public $aggregation_centers = []; // Stores aggregation center details (array of objects with name and volume sold)
    public $aggregation_center_sales; // Previous season volume in metric tonnes

    //2
    public $f_location_data = [];
    public $f_date_of_follow_up;
    public $f_area_under_cultivation = [];
    public $f_number_of_plantlets_produced = [];
    public $f_number_of_screen_house_vines_harvested;
    public $f_number_of_screen_house_min_tubers_harvested;
    public $f_number_of_sah_plants_produced;
    public $f_area_under_basic_seed_multiplication = [];
    public $f_area_under_certified_seed_multiplication = [];
    public $f_is_registered_seed_producer;
    public $f_seed_service_unit_registration_details = [];
    public $f_uses_certified_seed;
    public $f_market_segment = [];
    public $f_has_rtc_market_contract;
    public $f_total_vol_production_previous_season;
    public $f_total_production_value_previous_season = [];
    public $f_total_vol_irrigation_production_previous_season;
    public $f_total_irrigation_production_value_previous_season = [];
    public $f_sells_to_domestic_markets;
    public $f_sells_to_international_markets;
    public $f_uses_market_information_systems;
    public $f_market_information_systems;
    public $f_aggregation_centers = [];
    public $f_aggregation_center_sales;

    public $inputOne = [];

    public $inputTwo = [];

    public $inputThree = [];
    public $uuid;
    public function rules()
    {
        return [

        ];
    }

    public function save()
    {
        //   /      dd($this->pull());

        try {
            $uuid = Uuid::uuid4()->toString();
            if (isset($this->market_segment['fresh'])) {
                $this->market_segment['fresh'] = "YES";
            } else {
                $this->market_segment['fresh'] = "NO";
            }

            if (isset($this->market_segment['processed'])) {
                $this->market_segment['processed'] = "YES";
            } else {
                $this->market_segment['processed'] = "NO";
            }
            $firstTable = [
                'location_data' => $this->location_data,
                'date_of_recruitment' => $this->date_of_recruitment,
                'name_of_actor' => $this->name_of_actor,
                'name_of_representative' => $this->name_of_representative,
                'phone_number' => $this->phone_number,
                'type' => $this->type,
                'approach' => $this->approach, // For producer organizations only
                'sector' => $this->sector,
                'number_of_members' => $this->number_of_members, // For producer organizations only
                'group' => $this->group,
                'establishment_status' => $this->establishment_status,
                'is_registered' => $this->is_registered,
                'registration_details' => $this->registration_details,
                'number_of_employees' => $this->number_of_employees,

                'market_segment' => $this->market_segment, // Multiple market segments (array of strings)
                'has_rtc_market_contract' => $this->has_rtc_market_contract,
                'total_vol_production_previous_season' => $this->total_vol_production_previous_season, // Metric tonnes
                'total_production_value_previous_season' => $this->total_production_value_previous_season, // MWK
                'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season, // Metric tonnes
                'total_irrigation_production_value_previous_season' => $this->total_irrigation_production_value_previous_season, // MWK
                'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                'sells_to_international_markets' => $this->sells_to_international_markets,
                'uses_market_information_systems' => $this->uses_market_information_systems,
                'market_information_systems' => $this->market_information_systems,
                'aggregation_centers' => $this->aggregation_centers, // Stores aggregation center details (array of objects with name and volume sold)
                'aggregation_center_sales' => $this->aggregation_center_sales, // Previous season volume in metric tonnes
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
            ];

            //dd($firstTable);

            foreach ($firstTable as $key => $value) {
                if (is_array($value)) {
                    $firstTable[$key] = json_encode($value);
                }
            }

            $recruit = RtcProductionProcessor::create($firstTable);

            if (isset($this->f_market_segment['fresh'])) {
                $this->f_market_segment['fresh'] = "YES";
            } else {
                $this->f_market_segment['fresh'] = "NO";
            }

            if (isset($this->f_market_segment['processed'])) {
                $this->f_market_segment['processed'] = "YES";
            } else {
                $this->f_market_segment['processed'] = "NO";
            }
            $secondTable = [
                'rpm_processor_id' => $recruit->id,
                'location_data' => $this->location_data,
                'date_of_follow_up' => $this->f_date_of_follow_up,
                'area_under_cultivation' => $this->f_area_under_cultivation,
                'number_of_plantlets_produced' => $this->f_number_of_plantlets_produced,
                'number_of_screen_house_vines_harvested' => $this->f_number_of_screen_house_vines_harvested,
                'number_of_screen_house_min_tubers_harvested' => $this->f_number_of_screen_house_min_tubers_harvested,
                'number_of_sah_plants_produced' => $this->f_number_of_sah_plants_produced,
                'area_under_basic_seed_multiplication' => $this->f_area_under_basic_seed_multiplication,
                'area_under_certified_seed_multiplication' => $this->f_area_under_certified_seed_multiplication,
                'is_registered_seed_producer' => $this->f_is_registered_seed_producer,
                'seed_service_unit_registration_details' => $this->f_seed_service_unit_registration_details,
                'uses_certified_seed' => $this->f_uses_certified_seed,
                'market_segment' => $this->f_market_segment,
                'has_rtc_market_contract' => $this->f_has_rtc_market_contract,
                'total_vol_production_previous_season' => $this->f_total_vol_production_previous_season,
                'total_production_value_previous_season' => $this->f_total_production_value_previous_season,
                'total_vol_irrigation_production_previous_season' => $this->f_total_vol_irrigation_production_previous_season,
                'total_irrigation_production_value_previous_season' => $this->f_total_irrigation_production_value_previous_season,
                'sells_to_domestic_markets' => $this->f_sells_to_domestic_markets,
                'sells_to_international_markets' => $this->f_sells_to_international_markets,
                'uses_market_information_systems' => $this->f_uses_market_information_systems,
                'market_information_systems' => $this->f_market_information_systems,
                'aggregation_centers' => $this->f_aggregation_centers,
                'aggregation_center_sales' => $this->f_aggregation_center_sales,

            ];

            foreach ($secondTable as $key => $value) {
                if (is_array($value)) {
                    $secondTable[$key] = json_encode($value);
                }
            }

            RpmProcessorFollowUp::create($secondTable);
            $thirdTable = array();
            foreach ($this->inputOne as $index => $input) {

                $thirdTable[] = [

                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['conc_date_recorded'],
                    'partner_name' => $input['conc_partner_name'],
                    'country' => $input['conc_country'],
                    'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                    'product_type' => $input['conc_product_type'],
                    'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['conc_financial_value_of_sales'],
                ];
            }
            foreach ($thirdTable as $data) {
                RpmProcessorConcAgreement::create($data);
            }

            $fourthTable = [];

            foreach ($this->inputTwo as $index => $input) {

                $fourthTable[] = [
                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['dom_date_recorded'],
                    'crop_type' => $input['dom_crop_type'],
                    'market_name' => $input['dom_market_name'],
                    'district' => $input['dom_district'],
                    'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                    'product_type' => $input['dom_product_type'],
                    'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
                ];
            }

            foreach ($fourthTable as $input) {
                RpmProcessorDomMarket::create($input);
            }

            $fifthTable = [];
            foreach ($this->inputThree as $index => $input) {
                $fifthTable[] = [
                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['inter_date_recorded'],
                    'crop_type' => $input['inter_crop_type'],
                    'market_name' => $input['inter_market_name'],
                    'country' => $input['inter_country'],
                    'date_of_maximum_sale' => $input['inter_date_of_maximum_sale'],
                    'product_type' => $input['inter_product_type'],
                    'volume_sold_previous_period' => $input['inter_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['inter_financial_value_of_sales'],
                ];
            }

            foreach ($fifthTable as $input) {
                RpmProcessorInterMarket::create($input);
            }
            $this->alert('success', 'Submitted successfully!', [
                'toast' => false,
                'position' => 'center',
            ]);

            $this->reset();

            $this->redirect('#');

        } catch (\Exception $e) {
            # code...

            dd($e);
        }
    }

    public function resetall()
    {
        $this->reset();
        $this->inputOne[] = [
            'conc_date_recorded' => null,
            'conc_partner_name' => null,
            'conc_country' => null,
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => null,
            'conc_volume_sold_previous_period' => null,
            'conc_financial_value_of_sales' => null,
        ];
        $this->inputTwo[] = [
            'dom_date_recorded' => null,
            'dom_crop_type' => null,
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => null,
            'dom_volume_sold_previous_period' => null,
            'dom_financial_value_of_sales' => null,
        ];
        $this->inputThree[] = [
            'inter_date_recorded' => null,
            'inter_crop_type' => null,
            'inter_market_name' => null,
            'inter_country' => null,
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => null,
            'inter_volume_sold_previous_period' => null,
            'inter_financial_value_of_sales' => null,
        ];

    }

    public function addInputOne()
    {
        // Add a new set of empty values
        $this->inputOne[] = [
            'conc_date_recorded' => null,
            'conc_partner_name' => null,
            'conc_country' => null,
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => null,
            'conc_volume_sold_previous_period' => null,
            'conc_financial_value_of_sales' => null,
        ];
    }

    public function removeInputOne($index)
    {
        // Remove the set of values at the given index
        unset($this->inputOne[$index]);
        // Reindex the array to avoid issues with gaps in the keys
        // $this->inputOne = array_values($this->inputOne);
    }

    public function addInputTwo()
    {
        // Add a new set of empty values
        $this->inputTwo[] = [
            'dom_date_recorded' => null,
            'dom_crop_type' => null,
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => null,
            'dom_volume_sold_previous_period' => null,
            'dom_financial_value_of_sales' => null,
        ];
    }

    public function removeInputTwo($index)
    {
        // Remove the set of values at the given index
        unset($this->inputTwo[$index]);
        // Reindex the array to avoid issues with gaps in the keys
        // $this->inputOne = array_values($this->inputOne);
    }

    public function addInputThree()
    {
        // Add a new set of empty values
        $this->inputThree[] = [
            'inter_date_recorded' => null,
            'inter_crop_type' => null,
            'inter_market_name' => null,
            'inter_country' => null,
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => null,
            'inter_volume_sold_previous_period' => null,
            'inter_financial_value_of_sales' => null,
        ];
    }

    public function removeInputThree($index)
    {
        // Remove the set of values at the given index
        unset($this->inputThree[$index]);
        // Reindex the array to avoid issues with gaps in the keys
        // $this->inputOne = array_values($this->inputOne);
    }

    public function mount()
    {

        $this->fill(
            [
                'inputOne' =>
                collect([
                    [
                        'conc_date_recorded' => null,
                        'conc_partner_name' => null,
                        'conc_country' => null,
                        'conc_date_of_maximum_sale' => null,
                        'conc_product_type' => null,
                        'conc_volume_sold_previous_period' => null,
                        'conc_financial_value_of_sales' => null,
                    ],

                ]),

                'inputTwo' =>
                collect([
                    [
                        'dom_date_recorded' => null,
                        'dom_crop_type' => null,
                        'dom_market_name' => null,
                        'dom_district' => null,
                        'dom_date_of_maximum_sale' => null,
                        'dom_product_type' => null,
                        'dom_volume_sold_previous_period' => null,
                        'dom_financial_value_of_sales' => null,
                    ],
                ]),

                'inputThree' =>
                collect([
                    [
                        'inter_date_recorded' => null,
                        'inter_crop_type' => null,
                        'inter_market_name' => null,
                        'inter_country' => null,
                        'inter_date_of_maximum_sale' => null,
                        'inter_product_type' => null,
                        'inter_volume_sold_previous_period' => null,
                        'inter_financial_value_of_sales' => null,
                    ],
                ]),
            ]
        );

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add');
    }
}
