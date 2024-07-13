<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use App\Exceptions\UserErrorException;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public $date_of_follow_up;
    public $uuid;

    public $batch_no;

    public $forms = [];

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $openSubmission = true;
    public $routePrefix;
    public $aggregation_center_sales; // Previous season volume in metric tonnes
    public $group;

    public $show = false;
    public $recruits = [];

    public $selectedRecruit;

    public $recruit;

    public $f_name;
    public function rules()
    {

        $rules =
            [
                //first table
                'location_data.district' => 'required',
                'location_data.epa' => 'required',
                'location_data.enterprise' => 'required',
                'location_data.section' => 'required',
                'date_of_follow_up' => 'required|date',
                'location_data.group_name' => 'required',
                // 'aggregation_centers.specify' => 'required_if_accepted:aggregation_centers.response',
                // 'aggregation_center_sales' => 'required_if_accepted:aggregation_centers.response',
                'number_of_plantlets_produced.*' => 'required_if:group,EARLY GENERATION SEED PRODUCER',
                'market_information_systems' => 'required_if_accepted:uses_market_information_systems',
                // 'registration_details.*' => 'required_if_accepted:is_registered',
                'seed_service_unit_registration_details.*' => 'required_if_accepted:is_registered_seed_producer',
            ];



        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'district',
            'location_data.epa' => 'epa',
            'location_data.group_name' => 'group name',
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
            'is_registered_seed_producer' => 'registered seed producer',
            'seed_service_unit_registration_details.registration_number' => 'registration number',
            'seed_service_unit_registration_details.registration_date' => 'registration date',
            'number_of_plantlets_produced.cassava' => 'nop:cassava',
            'number_of_plantlets_produced.potato' => 'nop:potato',

            'number_of_plantlets_produced.sweet_potato' => 'nop:sweet potato',
            'aggregation_centers.specify' => 'aggregation centers specify',
            // 'aggregation_centers.response' => 'aggregation centers response',
            // 'aggregation_center_sales' => 'aggregation center sales',
            'market_information_systems' => 'market information systems',
            'uses_market_information_systems' => 'sell your products through market information systems',
        ];
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


    public function save()
    {
        try {

            $this->validate();


        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }



        try {



            try {


                $uuid = Uuid::uuid4()->toString();

                $secondTable = [];

                $secondTable = [


                    'rpm_farmer_id' => $this->recruit,
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
                    // 'has_rtc_market_contract' => $this->has_rtc_market_contract,
                    // 'total_vol_production_previous_season' => $this->total_vol_irrigation_production_previous_season,
                    // 'total_production_value_previous_season' => $this->total_production_value_previous_season,
                    // 'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season,
                    // 'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                    // 'sells_to_international_markets' => $this->sells_to_international_markets,
                    // 'uses_market_information_systems' => $this->uses_market_information_systems,
                    // 'market_information_systems' => $this->market_information_systems,
                    // 'aggregation_centers' => $this->aggregation_centers,


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

                RpmFarmerFollowUp::create($secondTable);


                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-farmers/view#followup">View Submission here</a>');
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
    public function mount($id)
    {

        $recruit = RtcProductionFarmer::find($id);
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
        $this->recruits = RtcProductionFarmer::distinct()->get();
        $this->routePrefix = Route::current()->getPrefix();
    }
    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.add-follow-up');
    }
}
