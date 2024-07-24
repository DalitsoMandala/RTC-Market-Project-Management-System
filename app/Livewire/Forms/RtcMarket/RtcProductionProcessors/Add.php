<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exceptions\UserErrorException;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\ManualDataAddedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Throwable;

class Add extends Component
{
    use LivewireAlert;
    public $form_name = 'RTC PRODUCTION AND MARKETING FORM PROCESSORS';
    public $selectedIndicator, $submissionPeriodId;
    public $location_data = [];
    public $date_of_recruitment;
    public $name_of_actor;
    public $name_of_representative;
    public $phone_number;
    public $type;
    public $approach; // For producer organizations only
    public $sector;
    public $number_of_members = [
        'total' => null,
        'female_18_35' => null,
        'female_35_plus' => null,
        'male_18_35' => null,
        'male_35_plus' => null,

    ]; // For producer organizations only
    public $group;
    public $establishment_status;
    public $is_registered = false;
    public $registration_details = [
        'registration_body' => null,
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $number_of_employees = [
        'formal' => [
            'total' => null,
            'female_18_35' => null,
            'female_35_plus' => null,
            'male_18_35' => null,
            'male_35_plus' => null,
        ],
        'informal' => [
            'total' => null,
            'female_18_35' => null,
            'female_35_plus' => null,
            'male_18_35' => null,
            'male_35_plus' => null,
        ],
    ];
    public $area_under_cultivation = [
        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,

    ]; // Stores area by variety (key-value pairs)
    public $number_of_plantlets_produced = [
        'cassava' => null,
        'potato' => null,
        'sweet_potato' => null,
    ];
    public $number_of_screen_house_vines_harvested; // Sweet potatoes
    public $number_of_screen_house_min_tubers_harvested; // Potatoes
    public $number_of_sah_plants_produced; // Cassava
    public $area_under_basic_seed_multiplication = [

        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,
        'variety_6' => null,
        'variety_7' => null,
    ]; // Acres
    public $area_under_certified_seed_multiplication = [

        'total' => null,
        'variety_1' => null,
        'variety_2' => null,
        'variety_3' => null,
        'variety_4' => null,
        'variety_5' => null,
        'variety_6' => null,
        'variety_7' => null,
    ]; // Acres
    public $is_registered_seed_producer = false;
    public $seed_service_unit_registration_details = [
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $uses_certified_seed = false;
    public $market_segment = [
        'fresh' => null,
        'processed' => null,
    ]; // Multiple market segments (array of strings)
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

    public $selectedForm,

    $selectedFinancialYear,
    $selectedMonth

    ;
    public $routePrefix;
    public $openSubmission = true;
    public function rules()
    {
        $rules = [
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.enterprise' => 'required',
            'location_data.section' => 'required',
            'date_of_recruitment' => 'required|date',
            'name_of_actor' => 'required',
            'name_of_representative' => 'required',
            'phone_number' => 'required',
            'type' => 'required',
            'sector' => 'required',
            'market_segment' => 'required', // Multiple market segments (array of strings)
            'group' => 'required',
            'registration_details.*' => 'required_if_accepted:is_registered',
            'number_of_members.*' => 'required_if:type,PRODUCER ORGANIZATION',
            'approach' => 'required_if:type,PRODUCER ORGANIZATION',
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
            'registration_details.registration_body' => 'registration body',
            'registration_details.registration_number' => 'registration number',
            'registration_details.registration_date' => 'registration date',
            'number_of_employees.formal.total' => 'Formal Employees Total',
            'number_of_employees.formal.female_18_35' => 'Formal Employees Female 18-35',
            'number_of_employees.formal.female_35_plus' => 'Formal Employees Female 35+',
            'number_of_employees.formal.male_18_35' => 'Formal Employees Male 18-35',
            'number_of_employees.formal.male_35_plus' => 'Formal Employees Male 35+',
            'number_of_employees.informal.total' => 'Informal Employees Total',
            'number_of_employees.informal.female_18_35' => 'Informal Employees Female 18-35',
            'number_of_employees.informal.female_35_plus' => 'Informal Employees Female 35+',
            'number_of_employees.informal.male_18_35' => 'Informal Employees Male 18-35',
            'number_of_employees.informal.male_35_plus' => 'Informal Employees Male 35+',
            'is_registered' => 'formally registered entity',
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

    public function validateDynamicForms()
    {


        $rules = [];
        $attributes = [];

        if ($this->has_rtc_market_contract) {
            $rules = array_merge($rules, [
                'inputOne.*.conc_date_recorded' => 'required',
                'inputOne.*.conc_partner_name' => 'required',
                'inputOne.*.conc_country' => 'required',
                'inputOne.*.conc_date_of_maximum_sale' => 'required',
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
        if ($name == 'inputTwo') {

            $this->fill(
                [


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


                ]
            );

        }
        if ($name == 'inputThree') {

            $this->fill(
                [

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
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
            ];

            //dd($firstTable);

            foreach ($firstTable as $key => $value) {
                if (is_array($value)) {

                    if (empty($value)) {
                        $secondTable[$key] = null;
                    } else {
                        $secondTable[$key] = json_encode($value);

                    }

                }
            }


            $recruit = RtcProductionProcessor::create($firstTable);
            $otherData = array();
            $otherData['main'] = $firstTable;
            $finalData = array_merge($otherData, $this->addMoreData($recruit));
            if (!$this->has_rtc_market_contract) {
                $finalData['agreement'] = []; //contractual agreement
            }
            if (!$this->sells_to_domestic_markets) {

                $finalData['market'] = []; //dom
            }
            if (!$this->sells_to_international_markets) {
                $finalData['intermarket'] = []; // international market
            }

            $currentUser = Auth::user();


            $table = ['rtc_production_processors', 'rpm_processor_follow_ups', 'rpm_processor_conc_agreements', 'rpm_processor_dom_markets', 'rpm_processor_inter_markets'];


            try {

                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    'data' => json_encode($finalData),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => json_encode($table),

                ]);


                //     $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                //   $currentUser->notify(new ManualDataAddedNotification($uuid, $link));

                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-processors/view">View Submission here</a>');
                return redirect()->to(url()->previous());

            } catch (UserErrorException $e) {
                // Log the actual error for debugging purposes
                \Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());
            }





        } catch (Throwable $th) {
            # code...

            session()->flash('error', 'Something went wrong!');
            \Log::error($th->getMessage());
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



    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);

        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);

        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();

            if ($submissionPeriod) {

                $this->openSubmission = true;

            } else {
                $this->openSubmission = false;
            }
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
        $this->routePrefix = Route::current()->getPrefix();
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add');
    }
}
