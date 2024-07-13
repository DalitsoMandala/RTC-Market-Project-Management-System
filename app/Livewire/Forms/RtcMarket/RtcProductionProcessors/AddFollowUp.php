<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exceptions\UserErrorException;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorFollowUp;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionProcessor;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Throwable;

class AddFollowUp extends Component
{
    use LivewireAlert;

    public $location_data = [];
    public $date_of_follow_up;
    public $area_under_cultivation = [
        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,
    ];
    public $number_of_plantlets_produced = [
        'cassava' => null,
        'potato' => null,
        'sweet_potato' => null,
    ];
    public $number_of_screen_house_vines_harvested;
    public $number_of_screen_house_min_tubers_harvested;
    public $number_of_sah_plants_produced;
    public $area_under_basic_seed_multiplication = [
        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,
        'variety_6' => null,
        'variety_7' => null,
    ];
    public $area_under_certified_seed_multiplication = [

        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,
        'variety_6' => null,
        'variety_7' => null,
    ];

    public $is_registered = false;
    public $registration_details = [
        'registration_body' => null,
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $is_registered_seed_producer = false;
    public $seed_service_unit_registration_details = [
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $uses_certified_seed = false;
    public $market_segment = [
        'fresh' => null,
        'processed' => null,
    ];
    public $has_rtc_market_contract = false;
    public $total_vol_production_previous_season;
    public $total_production_value_previous_season = [
        'total' => null,
        'date_of_maximum_sales' => null,
    ];
    public $total_vol_irrigation_production_previous_season;
    public $total_irrigation_production_value_previous_season = [
        'total' => null,
        'date_of_maximum_sales' => null,
    ];
    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;
    public $market_information_systems;
    public $aggregation_centers = [
        'response' => false,
        'specify' => null,
    ];
    public $aggregation_center_sales;
    public $inputOne = [];

    public $inputTwo = [];

    public $inputThree = [];
    public $name_of_actor;
    public $group;

    public $show = false;
    public $recruits = [];

    public $selectedRecruit;

    public $recruit;
    public $routePrefix;
    public $f_name;
    public function rules()
    {



        $rules = [
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.enterprise' => 'required',
            'location_data.section' => 'required',
            'date_of_follow_up' => 'required|date',
            'location_data.group_name' => 'required',
            'group' => 'required',
            'registration_details.*' => 'required_if_accepted:is_registered',

            'aggregation_centers.specify' => 'required_if_accepted:aggregation_centers.response',
            'aggregation_center_sales' => 'required_if_accepted:aggregation_centers.response',
            'market_information_systems' => 'required_if_accepted:uses_market_information_systems',

        ];
        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'district',
            'location_data.epa' => 'epa',
            'location_data.enterprise' => 'enterprise',
            'location_data.section' => 'section',
            'location_data.group_name' => 'group name',
            'registration_details.registration_body' => 'registration body',
            'registration_details.registration_number' => 'registration number',
            'registration_details.registration_date' => 'registration date',

            'is_registered' => 'formally registered entity',

            'is_registered_seed_producer' => 'registered seed producer',
            'seed_service_unit_registration_details.registration_number' => 'registration number',
            'seed_service_unit_registration_details.registration_date' => 'registration date',
            'number_of_members.total' => 'Total Members',
            'number_of_members.female_18_35' => 'Female Members 18-35',
            'number_of_members.female_35_plus' => 'Female Members 35+',
            'number_of_members.male_18_35' => 'Male Members 18-35',
            'number_of_members.male_35_plus' => 'Male Members 35+',
            'aggregation_centers.specify' => 'aggregation centers specify',
            'aggregation_centers.response' => 'aggregation centers response',
            'aggregation_center_sales' => 'aggregation center sales',
            'market_information_systems' => 'market information systems',
            'uses_market_information_systems' => 'sell your products through market information systems',

        ];
    }

    public function save()
    {
        try {

            $this->validate();


        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        $this->validateDynamicForms();

        try {


            try {
                # code...


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


                $secondTable = [
                    'rpm_processor_id' => $this->recruit,
                    'location_data' => $this->location_data,
                    'date_of_follow_up' => $this->date_of_follow_up,
                    'area_under_cultivation' => $this->area_under_cultivation,
                    'number_of_plantlets_produced' => $this->number_of_plantlets_produced,
                    'number_of_screen_house_vines_harvested' => $this->number_of_screen_house_vines_harvested,
                    'number_of_screen_house_min_tubers_harvested' => $this->number_of_screen_house_min_tubers_harvested,
                    'number_of_sah_plants_produced' => $this->number_of_sah_plants_produced,
                    'area_under_basic_seed_multiplication' => $this->area_under_basic_seed_multiplication,
                    'area_under_certified_seed_multiplication' => $this->area_under_certified_seed_multiplication,
                    'is_registered_seed_producer' => $this->is_registered_seed_producer,
                    'seed_service_unit_registration_details' => $this->seed_service_unit_registration_details,
                    'uses_certified_seed' => $this->uses_certified_seed,
                    'market_segment' => $this->market_segment,
                    'has_rtc_market_contract' => $this->has_rtc_market_contract,
                    'total_vol_production_previous_season' => $this->total_vol_production_previous_season,
                    'total_production_value_previous_season' => $this->total_production_value_previous_season,
                    'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season,
                    'total_irrigation_production_value_previous_season' => $this->total_irrigation_production_value_previous_season,
                    'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                    'sells_to_international_markets' => $this->sells_to_international_markets,
                    'uses_market_information_systems' => $this->uses_market_information_systems,
                    'market_information_systems' => $this->market_information_systems,
                    'aggregation_centers' => $this->aggregation_centers,
                    'aggregation_center_sales' => $this->aggregation_center_sales,

                ];

                foreach ($secondTable as $key => $value) {
                    if (is_array($value)) {

                        if (empty($value)) {
                            $secondTable[$key] = null;
                        } else {
                            $secondTable[$key] = json_encode($value);

                        }

                    }
                }

                RpmProcessorFollowUp::create($secondTable);
                $recruit = RtcProductionProcessor::find($this->recruit);
                $this->addMoreData($recruit);

                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-processors/view#followup">View Submission here</a>');
                return redirect()->to(url()->previous());

            } catch (UserErrorException $e) {
                // Log the actual error for debugging purposes
                \Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());


            }

        } catch (Throwable $th) {
            session()->flash('error', 'Something went wrong!');
            \Log::error($th->getMessage());

        }
    }


    public function resetValues($name) // be careful dont delete it will destroy alpinejs
    {


        if ($name == 'inputOne') {

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


                ]
            );


        }


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


    public function validateDynamicForms()
    {


        $rules = [];
        $attributes = [];

        if ($this->has_rtc_market_contract) {
            $rules = array_merge($rules, [
                'inputOne.*.conc_date_recorded' => 'required',
                'inputOne.*.conc_partner_name' => 'required',
                'inputOne.*.conc_country' => 'required',
                'inputOne.*.conc_date_of_maximum_sale' => 'required|date',
                'inputOne.*.conc_product_type' => 'required',
                'inputOne.*.conc_volume_sold_previous_period' => 'required',
                'inputOne.*.conc_financial_value_of_sales' => 'required',
            ]);

            $attributes = array_merge($attributes, [
                'inputOne.*.conc_date_recorded' => 'date recorded',
                'inputOne.*.conc_partner_name' => 'partner name',
                'inputOne.*.conc_country' => 'country',
                'inputOne.*.conc_date_of_maximum_sale' => 'date of maximum sale',
                'inputOne.*.conc_product_type' => 'product type',
                'inputOne.*.conc_volume_sold_previous_period' => 'volume sold previous period',
                'inputOne.*.conc_financial_value_of_sales' => 'financial value of sales',
            ]);
        }

        if ($this->sells_to_domestic_markets) {
            $rules = array_merge($rules, [
                'inputTwo.*.dom_date_recorded' => 'required',
                'inputTwo.*.dom_crop_type' => 'required',
                'inputTwo.*.dom_market_name' => 'required',
                'inputTwo.*.dom_district' => 'required',
                'inputTwo.*.dom_date_of_maximum_sale' => 'required',
                'inputTwo.*.dom_product_type' => 'required',
                'inputTwo.*.dom_volume_sold_previous_period' => 'required',
                'inputTwo.*.dom_financial_value_of_sales' => 'required',
            ]);

            $attributes = array_merge($attributes, [
                'inputTwo.*.dom_date_recorded' => 'date recorded',
                'inputTwo.*.dom_crop_type' => 'crop type',
                'inputTwo.*.dom_market_name' => 'market name',
                'inputTwo.*.dom_district' => 'district',
                'inputTwo.*.dom_date_of_maximum_sale' => 'date of maximum sale',
                'inputTwo.*.dom_product_type' => 'product type',
                'inputTwo.*.dom_volume_sold_previous_period' => 'volume sold previous period',
                'inputTwo.*.dom_financial_value_of_sales' => 'financial value of sales',
            ]);
        }

        if ($this->sells_to_international_markets) {
            $rules = array_merge($rules, [
                'inputThree.*.inter_date_recorded' => 'required',
                'inputThree.*.inter_crop_type' => 'required',
                'inputThree.*.inter_market_name' => 'required',
                'inputThree.*.inter_country' => 'required',
                'inputThree.*.inter_date_of_maximum_sale' => 'required',
                'inputThree.*.inter_product_type' => 'required',
                'inputThree.*.inter_volume_sold_previous_period' => 'required',
                'inputThree.*.inter_financial_value_of_sales' => 'required',
            ]);

            $attributes = array_merge($attributes, [
                'inputThree.*.inter_date_recorded' => 'date recorded',
                'inputThree.*.inter_crop_type' => 'crop type',
                'inputThree.*.inter_market_name' => 'market name',
                'inputThree.*.inter_country' => 'country',
                'inputThree.*.inter_date_of_maximum_sale' => 'date of maximum sale',
                'inputThree.*.inter_product_type' => 'product type',
                'inputThree.*.inter_volume_sold_previous_period' => 'volume sold previous period',
                'inputThree.*.inter_financial_value_of_sales' => 'financial value of sales',
            ]);
        }
        if (!empty($rules)) {
            try {

                $this->validate($rules, [], $attributes);


            } catch (Throwable $e) {
                session()->flash('validation_error', 'There are errors in the dynamic forms.');
                throw $e;
            }


        }


    }
    public function addMoreData($recruit)
    {
        try {
            $thirdTable = array();

            foreach ($this->inputOne as $index => $input) {

                $thirdTable[] = [

                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['conc_date_recorded'] ?? now(),
                    'partner_name' => $input['conc_partner_name'],
                    'country' => $input['conc_country'],
                    'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                    'product_type' => $input['conc_product_type'],
                    'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['conc_financial_value_of_sales'],

                ];
            }

            if ($this->has_rtc_market_contract) {
                RpmProcessorConcAgreement::insert($thirdTable);
            }

            $fourthTable = [];

            foreach ($this->inputTwo as $index => $input) {

                $fourthTable[] = [
                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['dom_date_recorded'] ?? now(),
                    'crop_type' => $input['dom_crop_type'],
                    'market_name' => $input['dom_market_name'],
                    'district' => $input['dom_district'],
                    'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                    'product_type' => $input['dom_product_type'],
                    'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
                ];
            }


            if ($this->sells_to_domestic_markets) {
                RpmProcessorDomMarket::insert($fourthTable);

            }



            $fifthTable = [];
            foreach ($this->inputThree as $index => $input) {
                $fifthTable[] = [
                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['inter_date_recorded'] ?? now(),
                    'crop_type' => $input['inter_crop_type'],
                    'market_name' => $input['inter_market_name'],
                    'country' => $input['inter_country'],
                    'date_of_maximum_sale' => $input['inter_date_of_maximum_sale'],
                    'product_type' => $input['inter_product_type'],
                    'volume_sold_previous_period' => $input['inter_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['inter_financial_value_of_sales'],
                ];
            }

            if ($this->sells_to_international_markets) {
                RpmProcessorInterMarket::insert($fifthTable);
            }

            return [

                "agreement" => $thirdTable,
                "market" => $fourthTable,
                "intermarket" => $fifthTable,

            ];


        } catch (UserErrorException $e) {
            # code...

            session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
        }

    }

    public function mount($id)
    {

        $recruit = RtcProductionProcessor::find($id);
        if ($recruit) {

            $this->location_data['epa'] = json_decode($recruit->location_data)->epa;
            $this->location_data['district'] = json_decode($recruit->location_data)->district;
            $this->location_data['enterprise'] = json_decode($recruit->location_data)->enterprise;
            $this->location_data['section'] = json_decode($recruit->location_data)->section;
            $this->recruit = $recruit->id;

            $this->group = $recruit->group;
            $this->show = true;
            $this->date_of_follow_up = date('Y-m-d');
            $this->f_name = $recruit->name_of_actor;
            $this->location_data['group_name'] = $recruit->name_of_actor;


        } else {
            abort(404);
        }
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
        $this->recruits = RtcProductionProcessor::distinct()->get();
        $this->routePrefix = Route::current()->getPrefix();
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add-follow-up');
    }
}
