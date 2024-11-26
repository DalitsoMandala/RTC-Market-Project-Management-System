<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use App\Exceptions\UserErrorException;
use App\Helpers\ExchangeRateHelper;
use App\Models\ExchangeRate;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Indicator;
use App\Models\OrganisationTarget;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\User;
use App\Notifications\ManualDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Throwable;

class Add extends Component
{
    use LivewireAlert;
    public $form_name = 'RTC PRODUCTION AND MARKETING FORM FARMERS';
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
    public $approach; // For producer organizations only
    public $sector;
    public $number_of_members = [
        //   'total' => null,
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
            // 'total' => null,
            'female_18_35' => null,
            'female_35_plus' => null,
            'male_18_35' => null,
            'male_35_plus' => null,
        ],
        'informal' => [
            //  'total' => null,
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
    ]; // Acres
    public $area_under_certified_seed_multiplication = [
    ]; // Acres
    public $is_registered_seed_producer = false;
    public $seed_service_unit_registration_details = [
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $uses_certified_seed = false;
    public $market_segment = [

    ]; // Multiple market segments (array of strings)
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

    public $rate = 0;

    public $targetSet = false;
    public $targetIds = [];
    public function rules()
    {

        $rules =
            [
                //first table
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
                // Multiple market segments (array of strings)
                'group' => 'required',
                'registration_details.*' => 'required_if_accepted:is_registered',
                'number_of_members.*' => 'required_if:type,Producer organization',
                'approach' => 'required_if:type,Producer organization',
                'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
                'total_vol_aggregation_center_sales' => 'required|numeric',
                'number_of_plantlets_produced.cassava' => 'required_if:group,Early generation seed producer',
                'number_of_plantlets_produced.potato' => 'required_if:group,Early generation seed producer',
                'number_of_plantlets_produced.sweet_potato' => 'required_if:group,Early generation seed producer',
                'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
                'seed_service_unit_registration_details.*' => 'required_if_accepted:is_registered_seed_producer',
                'area_under_cultivation.*.variety' => 'required|distinct',
                'area_under_cultivation.*.area' => 'required|numeric',
                'area_under_certified_seed_multiplication.*.variety' => 'required|distinct',
                'area_under_certified_seed_multiplication.*.area' => 'required|numeric',
                'number_of_employees.formal.female_18_35' => 'required|numeric',
                'number_of_employees.formal.female_35_plus' => 'required|numeric',
                'number_of_employees.formal.male_18_35' => 'required|numeric',
                'number_of_employees.formal.male_35_plus' => 'required|numeric',
                'number_of_employees.informal.female_18_35' => 'required|numeric',
                'number_of_employees.informal.female_35_plus' => 'required|numeric',
                'number_of_employees.informal.male_18_35' => 'required|numeric',
                'number_of_employees.informal.male_35_plus' => 'required|numeric',
                'market_segment' => 'required',
                'total_vol_production_previous_season' => 'required|numeric',
                'total_vol_irrigation_production_previous_season' => 'required|numeric',
                'total_production_value_previous_season.value' => 'required|numeric',
                'total_production_value_previous_season.date_of_maximum_sales' => 'required|date',
                'total_irrigation_production_value_previous_season.value' => 'required|numeric',
                'total_irrigation_production_value_previous_season.date_of_maximum_sales' => 'required|date',
                'area_under_basic_seed_multiplication.*.variety' => 'required_if:group,Early generation seed producer|distinct',
                'area_under_basic_seed_multiplication.*.area' => 'required_if:group,Early generation seed producer',
                'establishment_status' => 'required',

                'number_of_screen_house_vines_harvested' => 'required_if:group,Early generation seed producer',
                'number_of_screen_house_min_tubers_harvested' => 'required_if:group,Early generation seed producer',
                'number_of_sah_plants_produced' => 'required_if:group,Early generation seed producer',

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

            'number_of_members.female_18_35' => 'Female Members 18-35',
            'number_of_members.female_35_plus' => 'Female Members 35+',
            'number_of_members.male_18_35' => 'Male Members 18-35',
            'number_of_members.male_35_plus' => 'Male Members 35+',
            'aggregation_center_sales.*.name' => 'aggregation center sales name',
            'total_vol_aggregation_center_sales' => 'total aggregation center sales previous season',
            'market_information_systems.*.name' => 'market information systems name',
            'uses_market_information_systems' => 'sell your products through market information systems',
            'number_of_plantlets_produced.cassava' => 'number of plantlets produced (cassava)',
            'number_of_plantlets_produced.potato' => 'number of plantlets produced (potato)',
            'number_of_plantlets_produced.sweet_potato' => 'number of plantlets produced (sweet potato)',

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


            'area_under_basic_seed_multiplication.*.variety' => 'seed multiplication (variety)',
            'area_under_basic_seed_multiplication.*.area' => 'seed multiplication (area)',

        ];
    }



    public function resetValues($name) // be careful dont delete it will destroy alpinejs
    {

        $this->reset($name);


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


    public function removeAreaofCultivation($index)
    {
        unset($this->area_under_cultivation[$index]);
    }

    public function addAreaofCultivation()
    {
        $this->area_under_cultivation[] = [
            'variety' => null,
            'area' => null
        ];
    }


    public function removeBasicSeed($index)
    {
        unset($this->area_under_basic_seed_multiplication[$index]);
    }

    public function addBasicSeed()
    {
        $this->area_under_basic_seed_multiplication[] = [
            'variety' => null,
            'area' => null
        ];
    }

    public function removeCertifiedSeed($index)
    {
        unset($this->area_under_certified_seed_multiplication[$index]);
    }

    public function addCertifiedSeed()
    {
        $this->area_under_certified_seed_multiplication[] = [
            'variety' => null,
            'area' => null
        ];
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


    public function getExchangeRate($value, $date)
    {
        $exchangeRate = new ExchangeRateHelper();
        return $exchangeRate->getRate($value, $date);
    }

    public function updated($property, $value)
    {
        // Process the first set of data
        $this->processExchangeRate(
            'total_production_value_previous_season',
            $this->total_production_value_previous_season['value'] ?? null,
            $this->total_production_value_previous_season['date_of_maximum_sales'] ?? null
        );

        // Process the second set of data
        $this->processExchangeRate(
            'total_irrigation_production_value_previous_season',
            $this->total_irrigation_production_value_previous_season['value'] ?? null,
            $this->total_irrigation_production_value_previous_season['date_of_maximum_sales'] ?? null
        );
    }

    /**
     * Helper function to process exchange rates and update the given dataset.
     */
    protected function processExchangeRate($key, $value, $date)
    {
        if ($value && $date) {
            $rate = $this->getExchangeRate($value, $date);

            if ($rate === null) {
                $this->{$key}['date_of_maximum_sales'] = null;
                $this->{$key}['value'] = null;
                $this->{$key}['rate'] = null;
                $this->{$key}['total'] = null;
            } else {
                $totalValue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                $this->{$key}['rate'] = $rate;
                $this->{$key}['total'] = $totalValue;
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

            $period = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $this->selectedForm)
                ->where('financial_year_id', $this->selectedFinancialYear)->where('month_range_period_id', $this->selectedMonth)->first();
            if (!$period) {
                throw new UserErrorException("Sorry you can not submit your form right now!"); // expired or closed

            }


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
                //   'location_data' => $this->location_data,
                'epa' => $this->location_data['epa'],
                'district' => $this->location_data['district'],
                'section' => $this->location_data['section'],
                'enterprise' => $this->location_data['enterprise'],
                'date_of_recruitment' => $this->date_of_recruitment,
                'name_of_actor' => $this->name_of_actor,
                'name_of_representative' => $this->name_of_representative,
                'phone_number' => $this->phone_number,
                'type' => $this->type,
                'approach' => $this->approach, // For producer organizations only
                'sector' => $this->sector,
                //   'number_of_members' => $this->number_of_members, // For producer organizations only
                'group' => $this->group,
                'establishment_status' => $this->establishment_status,
                'is_registered' => $this->is_registered,
                'registration_body' => $this->registration_details['registration_body'],
                'registration_number' => $this->registration_details['registration_number'],
                'registration_date' => $this->registration_details['registration_date'],
                //  'registration_details' => $this->is_registered ? $this->registration_details : null,
                //  'number_of_employees' => $this->number_of_employees,
                // 'area_under_cultivation' => $this->area_under_cultivation, // Stores area by variety (key-value pairs)
                //  'number_of_plantlets_produced' => $this->group == 'Early generation seed producer' ? $this->number_of_plantlets_produced : 0,
                'number_of_plantlets_produced_cassava' => $this->group == 'Early generation seed producer' ? $this->number_of_plantlets_produced['cassava'] : 0,
                'number_of_plantlets_produced_potato' => $this->group == 'Early generation seed producer' ? $this->number_of_plantlets_produced['potato'] : 0,
                'number_of_plantlets_produced_sweet_potato' => $this->group == 'Early generation seed producer' ? $this->number_of_plantlets_produced['sweet_potato'] : 0,

                'number_of_screen_house_vines_harvested' => $this->group == 'Early generation seed producer' ? $this->number_of_screen_house_vines_harvested : 0, // Sweet potatoes
                'number_of_screen_house_min_tubers_harvested' => $this->group == 'Early generation seed producer' ? $this->number_of_screen_house_min_tubers_harvested : 0, // Potatoes
                'number_of_sah_plants_produced' => $this->group == 'Early generation seed producer' ? $this->number_of_sah_plants_produced : 0, // Cassava
                //  'area_under_basic_seed_multiplication' => $this->area_under_basic_seed_multiplication, // Acres
                // 'area_under_certified_seed_multiplication' => $this->area_under_certified_seed_multiplication, // Acres
                'is_registered_seed_producer' => $this->is_registered_seed_producer,

                'registration_number_seed_producer' => $this->seed_service_unit_registration_details['registration_number'],
                'registration_date_seed_producer' => $this->seed_service_unit_registration_details['registration_date'],
                //  'seed_service_unit_registration_details' => $this->is_registered_seed_producer ? $this->seed_service_unit_registration_details : null,
                'uses_certified_seed' => $this->uses_certified_seed,
                //  'market_segment' => $this->market_segment, // Multiple market segments (array of strings)
                'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
                'market_segment_processed' => $segment->contains('Processed') ? 1 : 0,
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
                //     'market_information_systems' => $this->market_information_systems,
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
                'status' => 'approved',
                'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
                //    'aggregation_centers' => $this->aggregation_center_sales, // Stores aggregation center details (array of objects with name and volume sold)
                'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales, // Previous season volume in metric tonnes
                'emp_formal_female_18_35' => $this->number_of_employees['formal']['female_18_35'],
                'emp_formal_male_18_35' => $this->number_of_employees['formal']['male_18_35'],
                'emp_formal_male_35_plus' => $this->number_of_employees['formal']['male_35_plus'],
                'emp_formal_female_35_plus' => $this->number_of_employees['formal']['female_35_plus'],
                'emp_informal_female_18_35' => $this->number_of_employees['informal']['female_18_35'],
                'emp_informal_male_18_35' => $this->number_of_employees['informal']['male_18_35'],
                'emp_informal_male_35_plus' => $this->number_of_employees['informal']['male_35_plus'],
                'emp_informal_female_35_plus' => $this->number_of_employees['informal']['female_35_plus'],
                'mem_female_18_35' => $this->number_of_members['female_18_35'],
                'mem_male_18_35' => $this->number_of_members['male_18_35'],
                'mem_male_35_plus' => $this->number_of_members['male_35_plus'],
                'mem_female_35_plus' => $this->number_of_members['female_35_plus'],

            ];





            //    dd($firstTable, $this->area_under_basic_seed_multiplication, $this->area_under_certified_seed_multiplication, $this->area_under_cultivation, $this->aggregation_center_sales);

            try {

                $currentUser = Auth::user();
                $recruit = RtcProductionFarmer::create($firstTable);
                $farmer = RtcProductionFarmer::find($recruit->id);
                //$farmer->members()->create($this->number_of_members);


                foreach ($this->area_under_cultivation as $data) {
                    $farmer->cultivatedArea()->create($data);
                }

                foreach ($this->area_under_basic_seed_multiplication as $data) {
                    $farmer->basicSeed()->create($data);
                }

                foreach ($this->area_under_certified_seed_multiplication as $data) {
                    $farmer->certifiedSeed()->create($data);
                }


                foreach ($this->market_information_systems as $data) {
                    $farmer->marketInformationSystems()->create($data);
                }


                foreach ($this->aggregation_center_sales as $data) {
                    $farmer->aggregationCenters()->create($data);
                }

                $this->addMoreData($recruit);




                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    // 'data' => json_encode($finalData),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => 'rtc_production_farmers',

                ]);




                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-farmers/view">View Submission here</a>');
                session()->flash('info', 'Your ID is: <b>' . substr($recruit->id, 0, 8) . '</b>' . '<br><br> Please keep this ID for future reference.');

                return redirect()->to(url()->previous());

            } catch (UserErrorException $e) {
                // Log the actual error for debugging purposes
                Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());
            }



        } catch (Throwable $th) {

            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
        }
    }


    public function addMoreData($recruit)
    {
        $dates = [
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 'approved'
        ];

        try {
            $thirdTable = array();

            foreach ($this->inputOne as $index => $input) {

                $thirdTable[] = [

                    'rpm_farmer_id' => $recruit->id,
                    'date_recorded' => $input['conc_date_recorded'] ?? now(),
                    'partner_name' => $input['conc_partner_name'],
                    'country' => $input['conc_country'],
                    'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                    'product_type' => $input['conc_product_type'],
                    'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['conc_financial_value_of_sales'],
                    ...$dates
                ];
            }

            if ($this->has_rtc_market_contract) {
                RpmFarmerConcAgreement::insert($thirdTable);
            }

            $fourthTable = [];

            foreach ($this->inputTwo as $index => $input) {

                $fourthTable[] = [
                    'rpm_farmer_id' => $recruit->id,
                    'date_recorded' => $input['dom_date_recorded'] ?? now(),
                    'crop_type' => $input['dom_crop_type'],
                    'market_name' => $input['dom_market_name'],
                    'district' => $input['dom_district'],
                    'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                    'product_type' => $input['dom_product_type'],
                    'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
                    ...$dates
                ];
            }


            if ($this->sells_to_domestic_markets) {
                RpmFarmerDomMarket::insert($fourthTable);

            }



            $fifthTable = [];
            foreach ($this->inputThree as $index => $input) {
                $fifthTable[] = [
                    'rpm_farmer_id' => $recruit->id,
                    'date_recorded' => $input['inter_date_recorded'] ?? now(),
                    'crop_type' => $input['inter_crop_type'],
                    'market_name' => $input['inter_market_name'],
                    'country' => $input['inter_country'],
                    'date_of_maximum_sale' => $input['inter_date_of_maximum_sale'],
                    'product_type' => $input['inter_product_type'],
                    'volume_sold_previous_period' => $input['inter_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['inter_financial_value_of_sales'],
                    ...$dates
                ];
            }

            if ($this->sells_to_international_markets) {
                RpmFarmerInterMarket::insert($fifthTable);
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
    public function resetall()
    {

        $this->reset();
        $this->fill(
            [
                'inputOne' =>
                    collect([
                        [
                            'conc_date_recorded' => null,
                            'conc_partner_name' => null,
                            'conc_country' => null,
                            'conc_date_of_maximum_sale' => null,
                            'conc_product_type' => 'Seed',
                            'conc_volume_sold_previous_period' => null,
                            'conc_financial_value_of_sales' => null,
                        ],

                    ]),

                'inputTwo' =>
                    collect([
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

                'inputThree' =>
                    collect([
                        [
                            'inter_date_recorded' => null,
                            'inter_crop_type' => 'Cassava',
                            'inter_market_name' => null,
                            'inter_country' => null,
                            'inter_date_of_maximum_sale' => null,
                            'inter_product_type' => 'Seed',
                            'inter_volume_sold_previous_period' => null,
                            'inter_financial_value_of_sales' => null,
                        ],
                    ]),
            ]
        );
    }

    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
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

                ->get();
            $user = User::find(auth()->user()->id);

            $targets = $target->pluck('id');
            $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)
                ->whereHas('submissionTarget', function ($query) use ($targets) {
                    $query->whereIn('submission_target_id', $targets);
                })
                ->get();
            $this->targetIds = $target->pluck('id')->toArray();

            if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

                $this->openSubmission = true;
                $this->targetSet = true;
            } else {
                $this->openSubmission = false;
                $this->targetSet = false;
            }
        }

        $this->fill(
            [
                'inputOne' =>
                    collect([
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

                'inputTwo' =>
                    collect([
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

                'inputThree' =>
                    collect([
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
                        'area' => null
                    ]
                ]),
                'area_under_basic_seed_multiplication' => collect([

                ]),
                'area_under_certified_seed_multiplication' => collect([
                    [
                        'variety' => null,
                        'area' => null
                    ]
                ]),
                'aggregation_center_sales' => collect([

                ]),
                'market_information_systems' => collect([

                ]),



                //'rate' => ExchangeRate::whereDate('date', date('Y-m-d'))->first()->rate ?? '',

            ]
        );
        $this->routePrefix = Route::current()->getPrefix();
        $this->total_production_value_previous_season['rate'] = $this->rate;
        $this->total_irrigation_production_value_previous_season['rate'] = $this->rate;

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.add');
    }
}