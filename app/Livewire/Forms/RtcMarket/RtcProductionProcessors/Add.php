<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exceptions\UserErrorException;
use App\Helpers\ExchangeRateHelper;
use App\Models\ExchangeRate;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\OrganisationTarget;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\User;
use App\Notifications\ManualDataAddedNotification;
use App\Traits\ManualDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Exception;
use Throwable;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $form_name = 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS';

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
    public $approach;  // For Producer organisations only
    public $sector;
    public $group;

    public $area_under_cultivation = [
        [
            'variety' => null,
            'area' => null
        ]
    ];  // Stores area by variety (key-value pairs)

    public $has_rtc_market_contract = false;
    public $market_segment = [];
    public $total_vol_production_previous_season;
    public $total_vol_production_previous_season_seed = null;
    public $total_vol_production_previous_season_cuttings = null;
    public $total_vol_production_previous_season_produce = null;
    public $total_production_value_previous_season_total = null;
    public $total_production_value_previous_season_date_of_maximum_sales = null;
    public $total_production_value_previous_season_rate = 0;
    public $total_production_value_previous_season_value = 0;
    public $total_production_value_previous_season_cuttings_value = null;
    public $total_production_value_previous_season_produce_value = null;
    public $total_production_value_previous_season_seed_value = null;
    public $total_production_value_previous_season_seed_bundle = null;
    public $total_production_value_previous_season_seed_prevailing_price = null;
    public $total_production_value_previous_season_cuttings_prevailing_price = null;
    public $total_production_value_previous_season_produce_prevailing_price = null;
    public $bundle_multiplier = 4;
    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;

    public $market_information_systems = [
        ['name' => null]
    ];

    public $sells_to_aggregation_centers = false;

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

    public $selectedForm,
        $selectedFinancialYear,
        $selectedMonth;

    public $routePrefix;
    public $targetSet = false;
    public $targetIds = [];
    public $rate = 0;
    public $date_of_followup;
    public $recruits;
    public $selectedRecruit;

    public function rules()
    {
        $rules = [
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.enterprise' => 'required',
            'location_data.section' => 'required',
            'location_data.group_name' => 'required',
            'date_of_followup' => 'required|date',
            'market_segment' => 'required',  // Multiple market segments (array of strings)
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
            'total_vol_production_previous_season' => 'required|numeric',
            'total_vol_production_previous_season_produce' => 'required|numeric',
            'total_vol_production_previous_season_seed' => 'required|numeric',
            'total_vol_production_previous_season_cuttings' => 'required|numeric',
            'total_production_value_previous_season_total' => 'required|numeric',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
        ];

        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'District',
            'location_data.epa' => 'EPA',
            'location_data.enterprise' => 'Enterprise',
            'location_data.section' => 'Section',
            'location_data.group_name' => 'Group Name',
            'date_of_followup' => 'Date of Followup',
            'market_segment' => 'Market Segment',
            'aggregation_center_sales.*.name' => 'Aggregation Center Sales',
            'total_vol_aggregation_center_sales' => 'Total Volume of Aggregation Center Sales',
            'market_information_systems.*.name' => 'Market Information Systems',
            'total_vol_production_previous_season' => 'Total Volume of Production Previous Season',
            'total_vol_production_previous_season_produce' => 'Total Volume of Production Previous Season (Produce)',
            'total_vol_production_previous_season_seed' => 'Total Volume of Production Previous Season (Seed)',
            'total_vol_production_previous_season_cuttings' => 'Total Volume of Production Previous Season (Cuttings)',
            'total_production_value_previous_season_total' => 'Financial value',
            'market_information_systems.*.name' => 'Market Information Systems',
            'aggregation_center_sales.*.name' => 'Aggregation Center Sales',
            'total_vol_aggregation_center_sales' => 'Total Volume of Aggregation Center Sales',
            'market_information_systems.*.name' => 'Market Information Systems',
            'aggregation_center_sales.*.name' => 'Aggregation Center Sales',
            'total_vol_aggregation_center_sales' => 'Total Volume of Aggregation Center Sales',
        ];
    }

    #[On('date-change')]
    public function realTimeDateOfFollowUp()
    {
        $this->total_production_value_previous_season_date_of_maximum_sales = $this->total_production_value_previous_season_date_of_maximum_sales;
    }

    public function validateDynamicForms()
    {
        $rules = [];
        $attributes = [];

        if ($this->has_rtc_market_contract) {
            // $rules = array_merge($rules, [
            //     'inputOne.*.conc_date_recorded' => 'required',
            //     'inputOne.*.conc_partner_name' => 'required',
            //     'inputOne.*.conc_country' => 'required',
            //     'inputOne.*.conc_date_of_maximum_sale' => 'required',
            //     'inputOne.*.conc_product_type' => 'required',
            //     'inputOne.*.conc_volume_sold_previous_period' => 'required',
            //     'inputOne.*.conc_financial_value_of_sales' => 'required',
            // ]);

            // $attributes = array_merge($attributes, [
            //     'inputOne.*.conc_date_recorded' => 'date recorded',
            //     'inputOne.*.conc_partner_name' => 'partner name',
            //     'inputOne.*.conc_country' => 'country',
            //     'inputOne.*.conc_date_of_maximum_sale' => 'date of maximum sale',
            //     'inputOne.*.conc_product_type' => 'product type',
            //     'inputOne.*.conc_volume_sold_previous_period' => 'volume sold previous period',
            //     'inputOne.*.conc_financial_value_of_sales' => 'financial value of sales',
            // ]);
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
        } catch (Exception $e) {
            // code...

            throw $e;
        }
    }

    public function getExchangeRate($value, $date)
    {
        $exchangeRate = new ExchangeRateHelper();
        return $exchangeRate->getRate($value, $date);
    }

    public function updated($property, $value) {}

    /**
     * Helper function to process exchange rates and update the given dataset.
     */
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
                    return;
                }
                // session()->flash('error', 'Exchange rate not found for the given date.');
            } else {
                $totalValue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = $rate;
                    $this->total_production_value_previous_season_total = $totalValue;
                    return;
                }
            }
        }
    }

    public function save()
    {
        try {
            $this->validate();
            $this->validateDynamicForms();
        } catch (Throwable $e) {
            //       session()->flash('validation_error', 'There are errors in the form.');
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }

        DB::beginTransaction();
        try {
            $uuid = Uuid::uuid4()->toString();

            $segment = collect($this->market_segment);

            $firstTable = [
                'epa' => $this->location_data['epa'],
                'district' => $this->location_data['district'],
                'section' => $this->location_data['section'],
                'enterprise' => $this->location_data['enterprise'],
                'group_name' => $this->location_data['group_name'],
                'date_of_followup' => $this->date_of_followup,
                'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
                'market_segment_processed' => $segment->contains('Processed') ? 1 : 0,  // Multiple market segments (array of strings)
                'has_rtc_market_contract' => $this->has_rtc_market_contract,
                'total_vol_production_previous_season' => $this->total_vol_production_previous_season,  // Metric tonnes
                'total_vol_production_previous_season_produce' => $this->total_vol_production_previous_season_produce ?? 0,  // Metric tonnes
                'total_vol_production_previous_season_seed' => $this->total_vol_production_previous_season_seed ?? 0,  // Metric tonnes
                'total_vol_production_previous_season_cuttings' => $this->total_vol_production_previous_season_cuttings ?? 0,  // Metric tonnes
                'prod_value_previous_season_total' => $this->total_production_value_previous_season_value ?? 0,
                'prod_value_previous_season_produce' => $this->total_production_value_previous_season_produce_value ?? 0,
                'prod_value_previous_season_seed' => $this->total_production_value_previous_season_seed_value ?? 0,
                'prod_value_previous_season_cuttings' => $this->total_production_value_previous_season_cuttings ?? 0,
                'prod_value_produce_prevailing_price' => $this->total_production_value_previous_season_cuttings_prevailing_price ?? 0,
                'prod_value_seed_prevailing_price' => $this->total_production_value_previous_season_produce_prevailing_price ?? 0,
                'prod_value_cuttings_prevailing_price' => $this->total_production_value_previous_season_seed_prevailing_price ?? 0,
                'prod_value_previous_season_date_of_max_sales' => $this->total_production_value_previous_season_date_of_maximum_sales,
                'prod_value_previous_season_usd_rate' => $this->total_production_value_previous_season_rate ?? 0,
                'prod_value_previous_season_usd_value' => $this->total_production_value_previous_season_total ?? 0,
                'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                'sells_to_international_markets' => $this->sells_to_international_markets,
                'uses_market_information_systems' => $this->uses_market_information_systems,
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
                'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
                'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales,  // Previous season volume in metric tonnes
                'status' => 'approved',
            ];

            $recruit = RtcProductionProcessor::create($firstTable);
            $processor = RtcProductionProcessor::find($recruit->id);

            $currentUser = Auth::user();

            foreach ($this->market_information_systems as $data) {
                if ($data['name'] == null) {
                    continue;
                }
                $processor->marketInformationSystems()->create($data);
            }

            if ($this->sells_to_aggregation_centers) {
                foreach ($this->aggregation_center_sales as $data) {
                    if ($data['name'] == null) {
                        continue;
                    }
                    $processor->aggregationCenters()->create($data);
                }
            }

            $this->addMoreData($recruit);

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

            $this->clearErrorBag();
            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-processors/view">View Submission here</a>',
            ]);

            DB::commit();
            //  $this->redirect(url()->previous());
        } catch (\Exception $th) {
            // code...
            DB::rollBack();

            $this->dispatch('show-alert', data: [
                'type' => 'error',
                'message' => 'Something went wrong!'
            ]);

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

    public function removeAreaofCultivation($index)
    {
        unset($this->area_under_cultivation[$index]);
        $this->area_under_cultivation = array_values($this->area_under_cultivation);
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
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add');
    }
}
