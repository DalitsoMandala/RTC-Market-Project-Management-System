<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use Throwable;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\ExchangeRate;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\OrganisationTarget;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorFollowUp;
use Illuminate\Support\Facades\Auth;
use App\Models\RpmProcessorDomMarket;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;

class Add extends Component
{
    use LivewireAlert;
    public $form_name = 'RTC PRODUCTION AND MARKETING FORM PROCESSORS';
    public $selectedIndicator, $submissionPeriodId;
    public $location_data = [
        'enterprise' => 'Cassava',
        'district' => null,
        'epa' => null,
        'section' => null,
    ];
    public $date_of_recruitment;
    public $name_of_actor;
    public $name_of_representative;
    public $phone_number;
    public $type;
    public $approach; // For Producer organisations only
    public $sector;
    public $number_of_members = [
        'total' => null,
        'female_18_35' => null,
        'female_35_plus' => null,
        'male_18_35' => null,
        'male_35_plus' => null,
    ]; // For Producer organisations only
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
    public $area_under_cultivation = []; // Stores area by variety (key-value pairs)
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
    public $market_segment = [];
    public $has_rtc_market_contract = false;
    public $total_vol_production_previous_season;
    public $total_production_value_previous_season = [
        'total' => null,
        'date_of_maximum_sales' => null,
        'rate' => 0,
        'value' => null,
    ];
    public $total_vol_irrigation_production_previous_season;
    public $total_irrigation_production_value_previous_season = [
        'total' => null,
        'date_of_maximum_sales' => null,
        'rate' => 0,
        'value' => null,
    ];
    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;
    public $market_information_systems = [];
    public $sells_to_aggregation_centers = false;
    public $aggregation_center_sales = []; // Previous season volume in metric tonnes
    public $total_vol_aggregation_center_sales;
    //2

    public $inputOne = [];

    public $inputTwo = [];

    public $inputThree = [];
    public $uuid;

    public $selectedForm, $selectedFinancialYear, $selectedMonth;

    public $routePrefix;
    public $openSubmission = true;
    public $targetSet = false;
    public $targetIds = [];
    public $rate = 0;
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
            'number_of_members.*' => 'required_if:type,Producer organisation',
            'approach' => 'required_if:type,Producer organisation',
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',

            'number_of_employees.formal.female_18_35' => 'required|numeric',
            'number_of_employees.formal.female_35_plus' => 'required|numeric',
            'number_of_employees.formal.male_18_35' => 'required|numeric',
            'number_of_employees.formal.male_35_plus' => 'required|numeric',
            'number_of_employees.informal.female_18_35' => 'required|numeric',
            'number_of_employees.informal.female_35_plus' => 'required|numeric',
            'number_of_employees.informal.male_18_35' => 'required|numeric',
            'number_of_employees.informal.male_35_plus' => 'required|numeric',
            'total_vol_production_previous_season' => 'required|numeric',
            'total_vol_irrigation_production_previous_season' => 'required|numeric',
            'total_production_value_previous_season.value' => 'required|numeric',
            'total_production_value_previous_season.date_of_maximum_sales' => 'required|date',
            'total_irrigation_production_value_previous_season.value' => 'required|numeric',
            'total_irrigation_production_value_previous_season.date_of_maximum_sales' => 'required|date',
            'establishment_status' => 'required',
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
            'aggregation_center_sales.*.name' => 'aggregation center sales name',
            'total_vol_aggregation_center_sales' => 'total aggregation center sales previous season',
            'market_information_systems.*.name' => 'market information systems',
            'uses_market_information_systems' => 'sell your products through market information systems',
            'area_under_cultivation.*.variety' => 'area under cultivation (variety)',
            'area_under_cultivation.*.area' => 'area under cultivation (area)',
            'area_under_certified_seed_multiplication.*.variety' => 'certified seed (variety)',
            'area_under_certified_seed_multiplication.*.area' => 'certified seed (area)',
            'total_vol_production_previous_season' => 'total volume of production previous season',
            'total_vol_irrigation_production_previous_season' => 'total volume of irrigation production previous season',
            'total_production_value_previous_season.value' => 'total value of production previous season',
            'total_production_value_previous_season.date_of_maximum_sales' => 'date of maximum sales of production previous season',
            'total_irrigation_production_value_previous_season.value' => 'total value of irrigation production previous season',
            'total_irrigation_production_value_previous_season.date_of_maximum_sales' => 'date of maximum sales of irrigation production previous season',
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

    public function resetValues($name)
    {
        // be careful dont delete it will destroy alpinejs
        $this->reset($name);
    }

    public function addMoreData($recruit)
    {
        $dates = [
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 'approved',
        ];
        try {
            $thirdTable = [];

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
                    ...$dates,
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
                    ...$dates,
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
                    ...$dates,
                ];
            }

            if ($this->sells_to_international_markets) {
                RpmProcessorInterMarket::insert($fifthTable);
            }

            return [
                'agreement' => $thirdTable,
                'market' => $fourthTable,
                'intermarket' => $fifthTable,
            ];
        } catch (UserErrorException $e) {
            # code...

            session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
        }
    }

    public function updated($property, $value)
    {
        if ($this->total_production_value_previous_season) {
            if ($this->total_production_value_previous_season['value'] && $this->total_production_value_previous_season['date_of_maximum_sales']) {
                $date = $this->total_production_value_previous_season['date_of_maximum_sales'];
                $value = $this->total_production_value_previous_season['value'];
                $rate = ExchangeRate::whereDate('date', date('Y-m-d'))->first()->rate ?? 1.0; // change this when you have historical data through exchange rate api

                $totalvalue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                $this->total_production_value_previous_season['rate'] = $rate;
                $this->total_production_value_previous_season['total'] = $totalvalue;
            }
        }

        if ($this->total_irrigation_production_value_previous_season) {
            if ($this->total_irrigation_production_value_previous_season['value'] && $this->total_irrigation_production_value_previous_season['date_of_maximum_sales']) {
                $date = $this->total_irrigation_production_value_previous_season['date_of_maximum_sales'];
                $value = $this->total_irrigation_production_value_previous_season['value'];
                $rate = ExchangeRate::whereDate('date', date('Y-m-d'))->first()->rate ?? 1.0; // change this when you have historical data through exchange rate api

                $totalvalue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                $this->total_irrigation_production_value_previous_season['rate'] = $rate;
                $this->total_irrigation_production_value_previous_season['total'] = $totalvalue;
            }
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

            foreach ($this->number_of_members as $key => $value) {
                $this->number_of_members[$key] = $value ? $value : 0;
            }

            foreach ($this->number_of_employees as $type => $group) {
                foreach ($group as $key => $value) {
                    // Check if the value is null and set it to 0
                    if (is_null($value)) {
                        $this->number_of_employees[$type][$key] = 0;
                    }
                }
            }

            $segment = collect($this->market_segment);

            $firstTable = [
                'epa' => $this->location_data['epa'],
                'district' => $this->location_data['district'],
                'section' => $this->location_data['section'],
                'enterprise' => $this->location_data['enterprise'],
                'date_of_recruitment' => $this->date_of_recruitment,
                'name_of_actor' => $this->name_of_actor,
                'name_of_representative' => $this->name_of_representative,
                'phone_number' => $this->phone_number,
                'type' => $this->type,
                'approach' => $this->approach, // For Producer organisations only
                'sector' => $this->sector,
                'mem_female_18_35' => $this->number_of_members['female_18_35'],
                'mem_male_18_35' => $this->number_of_members['male_18_35'],
                'mem_male_35_plus' => $this->number_of_members['male_35_plus'],
                'mem_female_35_plus' => $this->number_of_members['female_35_plus'], // For Producer organisations only
                'group' => $this->group,
                'establishment_status' => $this->establishment_status,
                'is_registered' => $this->is_registered,
                'registration_body' => $this->registration_details['registration_body'],
                'registration_number' => $this->registration_details['registration_number'],
                'registration_date' => $this->registration_details['registration_date'],
                'emp_formal_female_18_35' => $this->number_of_employees['formal']['female_18_35'],
                'emp_formal_male_18_35' => $this->number_of_employees['formal']['male_18_35'],
                'emp_formal_male_35_plus' => $this->number_of_employees['formal']['male_35_plus'],
                'emp_formal_female_35_plus' => $this->number_of_employees['formal']['female_35_plus'],
                'emp_informal_female_18_35' => $this->number_of_employees['informal']['female_18_35'],
                'emp_informal_male_18_35' => $this->number_of_employees['informal']['male_18_35'],
                'emp_informal_male_35_plus' => $this->number_of_employees['informal']['male_35_plus'],
                'emp_informal_female_35_plus' => $this->number_of_employees['informal']['female_35_plus'],
                'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
                'market_segment_processed' => $segment->contains('Processed') ? 1 : 0, // Multiple market segments (array of strings)
                'has_rtc_market_contract' => $this->has_rtc_market_contract,

                'total_vol_production_previous_season' => $this->total_vol_production_previous_season, // Metric tonnes
                'prod_value_previous_season_total' => $this->total_production_value_previous_season['value'],
                'prod_value_previous_season_date_of_max_sales' => $this->total_production_value_previous_season['date_of_maximum_sales'],
                'prod_value_previous_season_usd_rate' => $this->total_production_value_previous_season['rate'],
                'prod_value_previous_season_usd_value' => $this->total_production_value_previous_season['total'],

                'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season, // Metric tonnes
                'irr_prod_value_previous_season_total' => $this->total_irrigation_production_value_previous_season['value'],
                'irr_prod_value_previous_season_date_of_max_sales' => $this->total_irrigation_production_value_previous_season['date_of_maximum_sales'],
                'irr_prod_value_previous_season_usd_rate' => $this->total_irrigation_production_value_previous_season['rate'],
                'irr_prod_value_previous_season_usd_value' => $this->total_irrigation_production_value_previous_season['total'],

                'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                'sells_to_international_markets' => $this->sells_to_international_markets,
                'uses_market_information_systems' => $this->uses_market_information_systems,
                //     'market_information_systems' => $this->uses_market_information_systems ? $this->market_information_systems : null,
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
                'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
                //   'aggregation_centers' => $this->sells_to_aggregation_centers ? $this->aggregation_center_sales : null, // Stores aggregation center details (array of objects with name and volume sold)
                'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales, // Previous season volume in metric tonnes
                'status' => 'approved',
            ];

            //dd($firstTable);

            foreach ($firstTable as $key => $value) {
                if (is_array($value)) {
                    if (empty($value)) {
                        $firstTable[$key] = null;
                    } else {
                        $firstTable[$key] = json_encode($value);
                    }
                }
            }

            $recruit = RtcProductionProcessor::create($firstTable);
            $processor = RtcProductionProcessor::find($recruit->id);

            $currentUser = Auth::user();

            foreach ($this->market_information_systems as $data) {
                $processor->marketInformationSystems()->create($data);
            }

            foreach ($this->aggregation_center_sales as $data) {
                $processor->aggregationCenters()->create($data);
            }

            $this->addMoreData($recruit);

            try {
                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    //     'data' => json_encode($finalData),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => 'rtc_production_processors',
                ]);

                //     $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                //   $currentUser->notify(new ManualDataAddedNotification($uuid, $link));

                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-processors/view">View Submission here</a>');
                session()->flash('info', 'Your ID is: <b>' . substr($recruit->id, 0, 8) . '</b>' . '<br><br> Please keep this ID for future reference.');

                return redirect()->to(url()->previous());
            } catch (UserErrorException $e) {
                // Log the actual error for debugging purposes
                Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());
            }
        } catch (Throwable $th) {
            # code...
            dd($th);
            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
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
            'conc_country' => 'Malawi',
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => 'Seed',
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
            'dom_crop_type' => 'Cassava',
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => 'Seed',
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
            'inter_crop_type' => 'Cassava',
            'inter_market_name' => null,
            'inter_country' => 'Malawi',
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => 'Seed',
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
    public function removeSales($index)
    {
        unset($this->aggregation_center_sales[$index]);
    }

    public function addSales()
    {
        $this->aggregation_center_sales[] = [
            'name' => null,
        ];
    }

    public function removeMIS($index)
    {
        unset($this->market_information_systems[$index]);
    }

    public function addMIS()
    {
        $this->market_information_systems[] = [
            'name' => null,
        ];
    }
    public function removeAreaofCultivation($index)
    {
        unset($this->area_under_cultivation[$index]);
    }

    public function addAreaofCultivation()
    {
        $this->area_under_cultivation[] = [
            'variety' => null,
            'area' => null,
        ];
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

            $target = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->get();
            $user = User::find(auth()->user()->id);

            $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)->whereIn('submission_target_id', $target->pluck('id'))->get();
            $this->targetIds = $target->pluck('id')->toArray();


            if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

                $this->openSubmission = true;
                $this->targetSet = true;
            } else {
                $this->openSubmission = false;
                $this->targetSet = false;
            }
        }

        $this->fill([
            'inputOne' => collect([
                [
                    'conc_date_recorded' => null,
                    'conc_partner_name' => null,
                    'conc_country' => 'Malawi',
                    'conc_date_of_maximum_sale' => null,
                    'conc_product_type' => 'Seed',
                    'conc_volume_sold_previous_period' => null,
                    'conc_financial_value_of_sales' => null,
                ],
            ]),

            'inputTwo' => collect([
                [
                    'dom_date_recorded' => null,
                    'dom_crop_type' => 'Cassava',
                    'dom_market_name' => null,
                    'dom_district' => null,
                    'dom_date_of_maximum_sale' => null,
                    'dom_product_type' => 'Seed',
                    'dom_volume_sold_previous_period' => null,
                    'dom_financial_value_of_sales' => null,
                ],
            ]),

            'inputThree' => collect([
                [
                    'inter_date_recorded' => null,
                    'inter_crop_type' => 'Cassava',
                    'inter_market_name' => null,
                    'inter_country' => 'Malawi',
                    'inter_date_of_maximum_sale' => null,
                    'inter_product_type' => 'Seed',
                    'inter_volume_sold_previous_period' => null,
                    'inter_financial_value_of_sales' => null,
                ],
            ]),

            'area_under_cultivation' => collect([
                [
                    'variety' => null,
                    'area' => null,
                ],
            ]),

            'aggregation_center_sales' => collect([[]]),
            'market_information_systems' => collect([[]]),
            'rate' => ExchangeRate::whereDate('date', date('Y-m-d'))->first()->rate ?? '',
        ]);
        $this->routePrefix = Route::current()->getPrefix();
        $this->total_production_value_previous_season['rate'] = $this->rate;
        $this->total_irrigation_production_value_previous_season['rate'] = $this->rate;
    }

    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add');
    }
}
