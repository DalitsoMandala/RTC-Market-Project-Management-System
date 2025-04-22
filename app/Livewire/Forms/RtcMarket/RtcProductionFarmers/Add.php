<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use ReflectionClass;
use Ramsey\Uuid\Uuid;
use App\Models\Project;
use Livewire\Component;
use ReflectionProperty;
use App\Models\Indicator;
use App\Models\Submission;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use App\Models\ExchangeRate;
use App\Models\FinancialYear;
use App\Traits\ManualDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\RpmFarmerFollowUp;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExchangeRateHelper;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmFarmerInterMarket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\RpmFarmerConcAgreement;
use App\Models\HouseholdRtcConsumption;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Notifications\ManualDataAddedNotification;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $form_name = 'RTC PRODUCTION AND MARKETING FORM FARMERS';

    public $selectedIndicator,
        $submissionPeriodId;

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
    public $approach;  // For producer organizations only
    public $sector;

    public $number_of_members = [
        //   'total' => null,
        'female_18_35' => null,
        'female_35_plus' => null,
        'male_18_35' => null,
        'male_35_plus' => null,
    ];  // For producer organizations only

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

    public $area_under_cultivation = [
        [
            'variety' => null,
            'area' => null
        ]
    ];  // Stores area by variety (key-value pairs)

    public $number_of_plantlets_produced = [
        'cassava' => null,
        'potato' => null,
        'sweet_potato' => null,
    ];

    public $number_of_screen_house_vines_harvested;  // Sweet potatoes
    public $number_of_screen_house_min_tubers_harvested;  // Potatoes
    public $number_of_sah_plants_produced;  // Cassava
    public $area_under_basic_seed_multiplication = [
        [
            'variety' => null,
            'area' => null
        ]
    ];  // Acres
    public $area_under_certified_seed_multiplication = [
        [
            'variety' => null,
            'area' => null
        ]
    ];  // Acres
    public $is_registered_seed_producer = false;

    public $seed_service_unit_registration_details = [
        [
            'registration_number' => null,
            'registration_date' => null,
        ]

    ];

    public $uses_certified_seed = false;
    public $market_segment = [];
    public $has_rtc_market_contract = false;
    public $total_vol_production_previous_season;
    public $total_vol_production_previous_season_seed = null;

    public $total_vol_production_previous_season_cuttings = null;

    public $total_vol_production_previous_season_produce = null;

    // public $total_production_value_previous_season = [
    //     [
    //         'total' => null,
    //         'date_of_maximum_sales' => null,
    //         'rate' => 0,
    //         'value' => 0,
    //         'cuttings_value' => null,
    //         'produce_value' => null,
    //         'seed_value' => null,
    //         'cuttings_prevailing_price' => null,
    //         'produce_prevailing_price' => null,
    //         'seed_prevailing_price' => null,
    //     ]

    // ];

    public $total_production_value_previous_season_total = 0;
    public $total_production_value_previous_season_date_of_maximum_sales = null;
    public $total_production_value_previous_season_rate = 0;
    public $total_production_value_previous_season_value = 0;
    public $total_production_value_previous_season_cuttings_value = null;
    public $total_production_value_previous_season_produce_value = null;
    public $total_production_value_previous_season_seed_value = null;
    public $total_irrigation_production_value_previous_season_seed_bundle = null;
    public $total_production_value_previous_season_seed_prevailing_price = null;
    public $total_production_value_previous_season_cuttings_prevailing_price = null;
    public $total_production_value_previous_season_produce_prevailing_price = null;



    public $total_vol_irrigation_production_previous_season;

    public $total_vol_irrigation_production_previous_season_seed = null;

    public $total_vol_irrigation_production_previous_season_cuttings = null;

    public $total_vol_irrigation_production_previous_season_produce = null;


    public $total_irrigation_production_value_previous_season = [
        [
            'total' => null,
            'date_of_maximum_sales' => null,
            'rate' => 0,
            'value' => 0,
            'cuttings_value' => null,
            'produce_value' => null,
            'seed_value' => null,
            'cuttings_prevailing_price' => null,
            'produce_prevailing_price' => null,
            'seed_prevailing_price' => null,
        ]

    ];

    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;
    public $market_information_systems = [
        ['name' => null]
    ];
    public $sells_to_aggregation_centers = true;
    public $aggregation_center_sales = [
        ['name' => null]
    ];  // Previous season volume in metric tonnes
    public $total_vol_aggregation_center_sales;
    // 2
    public $inputOne = [
        [
            'conc_date_recorded' => null,
            'conc_partner_name' => null,
            'conc_country' => null,
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => 'Seed',
            'conc_volume_sold_previous_period' => null,
            'conc_financial_value_of_sales' => null,
        ],
    ];
    public $inputTwo = [
        [
            'dom_date_recorded' => null,
            'dom_crop_type' => 'Cassava',
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => 'Seed',
            'dom_volume_sold_previous_period' => null,
            'dom_financial_value_of_sales' => null,
        ]
    ];
    public $inputThree = [
        [
            'inter_date_recorded' => null,
            'inter_crop_type' => 'Cassava',
            'inter_market_name' => null,
            'inter_country' => 'Malawi',
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => 'Seed',
            'inter_volume_sold_previous_period' => null,
            'inter_financial_value_of_sales' => null,
        ]
    ];
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
    public $openSubmission = false;
    public $routePrefix;
    public $rate = 0;
    public $targetSet = false;
    public $targetIds = [];
    public $date_of_followup;
    public $registrations = [
        ['variety' => null, 'reg_date' => null, 'reg_no' => null],
    ];
    public function rules()
    {
        $rules =
            [
                // first table
                'location_data.group_name' => 'required',
                'location_data.enterprise' => 'required',
                'location_data.district' => 'required',
                'location_data.epa' => 'required',
                'location_data.section' => 'required',
                'date_of_followup' => 'required|date',
                'area_under_cultivation.*.variety' => 'required|distinct',
                'area_under_cultivation.*.area' => 'required|numeric',
                'number_of_plantlets_produced.*' => 'nullable|numeric',
                'number_of_screen_house_vines_harvested' => 'nullable|numeric',
                'number_of_screen_house_min_tubers_harvested' => 'nullable|numeric',
                'number_of_sah_plants_produced' => 'nullable|numeric',
                'area_under_basic_seed_multiplication.*.variety' => 'nullable|distinct',
                'area_under_basic_seed_multiplication.*.area' => 'nullable|numeric',
                'area_under_certified_seed_multiplication.*.variety' => 'nullable|distinct',
                'area_under_certified_seed_multiplication.*.area' => 'nullable|numeric',
                'market_segment' => 'required',
                'total_vol_production_previous_season' => 'required|numeric',
                'total_vol_production_previous_season_produce' => 'required|numeric',
                'total_vol_production_previous_season_seed' => 'required|numeric',
                'total_vol_production_previous_season_cuttings' => 'required|numeric',
                'total_production_value_previous_season.value' => 'required|numeric',
                'total_production_value_previous_season.total' => 'required|numeric',
                'total_production_value_previous_season.date_of_maximum_sales' => 'required|date',

                'total_vol_irrigation_production_previous_season' => 'required|numeric',
                'total_vol_irrigation_production_previous_season_produce' => 'required|numeric',
                'total_vol_irrigation_production_previous_season_seed' => 'required|numeric',
                'total_vol_irrigation_production_previous_season_cuttings' => 'required|numeric',
                'total_irrigation_production_value_previous_season.total' => 'required|numeric',
                'total_irrigation_production_value_previous_season.value' => 'required|numeric',
                'total_irrigation_production_value_previous_season.date_of_maximum_sales' => 'required|date',
                'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
                'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
                'total_vol_aggregation_center_sales' => 'required|numeric',
                'registrations.*' => 'required_if_accepted:is_registered_seed_producer',

            ];

        return $rules;
    }


    #[On('update-form')]
    public function FooBar()
    {
        return;
    }
    public function SellsToAggregationCenters($value)
    {
        if (!$value) {
            $this->total_vol_aggregation_center_sales = 0;
        }
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
            'registrations.*.variety' => 'variety',
            'registrations.*.reg_date' => 'registration date',
            'registrations.*.reg_no' => 'registration number',
        ];
    }

    public function addRegistration()
    {
        $this->registrations[] = ['variety' => '', 'reg_date' => '', 'reg_no' => ''];
    }

    public function removeRegistration($index)
    {
        unset($this->registrations[$index]);
        $this->registrations = array_values($this->registrations); // Reindex
    }

    public function resetValues($name)  // be careful dont delete it will destroy alpinejs
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
        $this->inputOne = array_values($this->inputOne);
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
        $this->inputTwo = array_values($this->inputTwo);
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
        $this->inputThree = array_values($this->inputThree);
    }

    public function removeAreaofCultivation($index)
    {
        unset($this->area_under_cultivation[$index]);
        $this->area_under_cultivation = array_values($this->area_under_cultivation);
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
        $this->area_under_basic_seed_multiplication = array_values($this->area_under_basic_seed_multiplication);
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
        $this->area_under_certified_seed_multiplication = array_values($this->area_under_certified_seed_multiplication);
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
        $this->aggregation_center_sales = array_values($this->aggregation_center_sales);
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
        $this->market_information_systems = array_values($this->market_information_systems);
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



    public function exchangeRateCalculateProduction()
    {


        $this->processExchangeRate(
            'total_production_value_previous_season',
            $this->total_production_value_previous_season_value ?? null,
            $this->total_production_value_previous_season_date_of_maximum_sales ?? null
        );
    }



    //  #[On('date-set')]
    public function updatedDateOfFollowUp($value)
    {


        $this->total_production_value_previous_season_date_of_maximum_sales = $value;
        $this->total_irrigation_production_value_previous_season['date_of_maximum_sales'] = $value;
    }

    public function exchangeRateCalculateIrrigation()
    {

        // Process the second set of data
        $this->processExchangeRate(
            'total_irrigation_production_value_previous_season',
            $this->total_irrigation_production_value_previous_season['value'] ?? null,
            $this->total_production_value_previous_season['date_of_maximum_sales'] ?? null
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

                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = 0;
                    $this->total_production_value_previous_season_total = 0;
                } else {
                    // $this->total_irrigation_production_value_previous_season_rate = 0;
                    // $this->total_irrigation_production_value_previous_season_total = 0;
                }
                //  session()->flash('validation_error', 'Exchange rate not found for the given date.');
            } else {

                $totalValue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = $rate;
                    $this->total_production_value_previous_season_total = $totalValue;
                } else {
                    // $this->total_irrigation_production_value_previous_season_rate = $rate;
                    // $this->total_irrigation_production_value_previous_season_total = $totalValue;
                }
                //    $this->{$key}['rate'] = $rate;
                //    $this->{$key}['total'] = $totalValue;
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


        DB::beginTransaction();
        try {
            $uuid = Uuid::uuid4()->toString();

            $period = SubmissionPeriod::where('is_open', true)
                ->where('is_expired', false)
                ->where('form_id', $this->selectedForm)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->first();
            if (!$period) {
                throw new UserErrorException('Sorry you can not submit your form right now!');  // expired or closed
            }



            $segment = collect($this->market_segment);

            $firstTable = [
                //   'location_data' => $this->location_data,
                'epa' => $this->location_data['epa'],
                'district' => $this->location_data['district'],
                'section' => $this->location_data['section'],
                'enterprise' => $this->location_data['enterprise'],
                'group_name' => $this->location_data['group_name'],
                'date_of_followup' => $this->date_of_followup,
                'number_of_plantlets_produced_cassava' => $this->number_of_plantlets_produced['cassava'] ?? 0,
                'number_of_plantlets_produced_potato' => $this->number_of_plantlets_produced['potato'] ?? 0,
                'number_of_plantlets_produced_sweet_potato' => $this->number_of_plantlets_produced['sweet_potato'] ?? 0,
                'number_of_screen_house_vines_harvested' => $this->number_of_screen_house_vines_harvested ?? 0,  // Sweet potatoes
                'number_of_screen_house_min_tubers_harvested' => $this->number_of_screen_house_min_tubers_harvested ?? 0,  // Potatoes
                'number_of_sah_plants_produced' => $this->number_of_sah_plants_produced ?? 0,  // Cassava
                'is_registered_seed_producer' => $this->is_registered_seed_producer,
                'uses_certified_seed' => $this->uses_certified_seed,
                'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
                'market_segment_processed' => $segment->contains('Processed') ? 1 : 0,
                'market_segment_seed' => $segment->contains('Seed') ? 1 : 0,
                'market_segment_cuttings' => $segment->contains('Cuttings') ? 1 : 0,
                'has_rtc_market_contract' => $this->has_rtc_market_contract,
                'total_vol_production_previous_season' => $this->total_vol_production_previous_season ?? 0,  // Metric tonnes
                'total_vol_production_previous_season_produce' => $this->total_vol_production_previous_season_produce ?? 0,  // Metric tonnes
                'total_vol_production_previous_season_seed' => $this->total_vol_production_previous_season_seed ?? 0,  // Metric tonnes
                'total_vol_production_previous_season_cuttings' => $this->total_vol_production_previous_season_cuttings ?? 0,  // Metric tonnes

                'prod_value_previous_season_total' => $this->total_production_value_previous_season['value'] ?? 0,
                'prod_value_previous_season_produce' => $this->total_production_value_previous_season['produce_value'] ?? 0,
                'prod_value_previous_season_seed' => $this->total_production_value_previous_season['seed_value'] ?? 0,
                'prod_value_previous_season_cuttings' => $this->total_production_value_previous_season['cuttings_value'] ?? 0,
                'prod_value_produce_prevailing_price' => $this->total_production_value_previous_season['cuttings_prevailing_price'] ?? 0,
                'prod_value_seed_prevailing_price' => $this->total_production_value_previous_season['produce_prevailing_price'] ?? 0,
                'prod_value_cuttings_prevailing_price' => $this->total_production_value_previous_season['seed_prevailing_price'] ?? 0,
                'prod_value_previous_season_date_of_max_sales' => $this->total_production_value_previous_season['date_of_maximum_sales'],
                'prod_value_previous_season_usd_rate' => $this->total_production_value_previous_season['rate'],
                'prod_value_previous_season_usd_value' => $this->total_production_value_previous_season['total'],

                'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season ?? 0,  // Metric tonnes
                'total_vol_irrigation_production_previous_season_produce' => $this->total_vol_irrigation_production_previous_season_produce ?? 0,  // Metric tonnes
                'total_vol_irrigation_production_previous_season_seed' => $this->total_vol_irrigation_production_previous_season_seed ?? 0,  // Metric tonnes
                'total_vol_irrigation_production_previous_season_cuttings' => $this->total_vol_irrigation_production_previous_season_cuttings ?? 0,  // Metric tonnes

                'irr_prod_value_previous_season_total' => $this->total_irrigation_production_value_previous_season['value'] ?? 0,
                'irr_prod_value_previous_season_produce' => $this->total_irrigation_production_value_previous_season['produce_value'] ?? 0,
                'irr_prod_value_previous_season_seed' => $this->total_irrigation_production_value_previous_season['seed_value'] ?? 0,
                'irr_prod_value_previous_season_cuttings' => $this->total_irrigation_production_value_previous_season['cuttings_value'] ?? 0,
                'irr_prod_value_produce_prevailing_price' => $this->total_irrigation_production_value_previous_season['cuttings_prevailing_price'] ?? 0,
                'irr_prod_value_seed_prevailing_price' => $this->total_irrigation_production_value_previous_season['produce_prevailing_price'] ?? 0,
                'irr_prod_value_cuttings_prevailing_price' => $this->total_irrigation_production_value_previous_season['seed_prevailing_price'] ?? 0,
                'irr_prod_value_previous_season_date_of_max_sales' => $this->total_irrigation_production_value_previous_season['date_of_maximum_sales'],
                'irr_prod_value_previous_season_usd_rate' => $this->total_irrigation_production_value_previous_season['rate'],
                'irr_prod_value_previous_season_usd_value' => $this->total_irrigation_production_value_previous_season['total'],

                'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                'sells_to_international_markets' => $this->sells_to_international_markets,
                'uses_market_information_systems' => $this->uses_market_information_systems,
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
                'status' => 'approved',
                'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
                'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales,  // Previous season volume in metric tonnes

            ];



            $currentUser = Auth::user();
            $recruit = RtcProductionFarmer::create($firstTable);
            $farmer = RtcProductionFarmer::find($recruit->id);
            // $farmer->members()->create($this->number_of_members);

            foreach ($this->area_under_cultivation as $data) {
                $farmer->cultivatedArea()->create($data);
            }

            foreach ($this->area_under_basic_seed_multiplication as $data) {
                if ($data['area'] == null && $data['variety'] == null) {
                    continue;
                }
                $farmer->basicSeed()->create($data);
            }

            foreach ($this->area_under_certified_seed_multiplication as $data) {
                if ($data['area'] == null && $data['variety'] == null) {
                    continue;
                }
                $farmer->certifiedSeed()->create($data);
            }


            foreach ($this->market_information_systems as $data) {
                if ($data['name'] == null) {
                    continue;
                }
                $farmer->marketInformationSystems()->create($data);
            }


            if ($this->sells_to_aggregation_centers) {
                foreach ($this->aggregation_center_sales as $data) {
                    if ($data['name'] == null) {
                        continue;
                    }
                    $farmer->aggregationCenters()->create($data);
                }
            }


            if ($this->is_registered_seed_producer) {
                foreach ($this->registrations as $data) {
                    $farmer->registeredSeed()->create($data);
                }
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
            DB::commit();
            $this->dispatch('clear-drafts');
            $this->redirect(url()->previous());
        } catch (Throwable $th) {
            DB::rollBack();
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

        // return [
        //     'agreement' => $thirdTable,
        //     'market' => $fourthTable,
        //     'intermarket' => $fifthTable,
        // ];
    }

    public function resetall()
    {
        $this->reset();
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

        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();
    }


    public function saveDraft()
    {


        $user = auth()->user()->id;
        $form = Form::find($this->selectedForm);
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.add');
    }
}
