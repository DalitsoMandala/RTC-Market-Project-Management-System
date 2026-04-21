<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Project;
use Livewire\Component;
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
use App\Helpers\DistrictObject;
use App\Models\RpmFarmerConcAgreement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $form_name = 'RTC PRODUCTION AND MARKETING FORM FARMERS';

    // Core Properties
    public $uniqueId;
    public $uuid;
    public $batch_no;
    public $routePrefix;
    public $openSubmission = false;

    // Component State
    public $selectedIndicator, $submissionPeriodId, $selectedForm, $selectedMonth, $selectedFinancialYear, $selectedProject;
    public $forms = [], $months = [], $financialYears = [], $projects = [], $targetIds = [];
    public $rate = 0;
    public $targetSet = false;

    // Farmer & Location Data
    public $location_data = ['enterprise' => 'Cassava', 'district' => null, 'epa' => null, 'section' => null];
    public $date_of_recruitment, $name_of_actor, $name_of_representative, $phone_number, $type, $approach, $sector, $category, $group, $establishment_status;
    public $is_registered = false;
    public $registration_details = ['registration_body' => null, 'registration_number' => null, 'registration_date' => null];
    public $number_of_members = ['female_18_35' => null, 'female_35_plus' => null, 'male_18_35' => null, 'male_35_plus' => null];
    public $number_of_employees = [
        'formal' => ['female_18_35' => null, 'female_35_plus' => null, 'male_18_35' => null, 'male_35_plus' => null],
        'informal' => ['female_18_35' => null, 'female_35_plus' => null, 'male_18_35' => null, 'male_35_plus' => null],
    ];

    // Production Data
    public $date_of_followup;
    public $number_of_plantlets_produced = ['cassava' => null, 'potato' => null, 'sweet_potato' => null];
    public $number_of_screen_house_vines_harvested;
    public $number_of_screen_house_min_tubers_harvested;
    public $number_of_sah_plants_produced;

    // Previous Season Volumes & Values (Rainfed)
    public $total_vol_production_previous_season, $total_vol_production_previous_season_seed, $total_vol_production_previous_season_cuttings, $total_vol_production_previous_season_produce;
    public $total_production_value_previous_season_total, $total_production_value_previous_season_date_of_maximum_sales, $total_production_value_previous_season_rate = 0, $total_production_value_previous_season_value = 0;
    public $total_production_value_previous_season_cuttings_value, $total_production_value_previous_season_produce_value, $total_production_value_previous_season_seed_value;
    public $total_production_value_previous_season_seed_bundle, $total_production_value_previous_season_seed_prevailing_price, $total_production_value_previous_season_cuttings_prevailing_price, $total_production_value_previous_season_produce_prevailing_price;
    public $bundle_multiplier = 4;

    // Previous Season Volumes & Values (Irrigation)
    public $total_vol_irrigation_production_previous_season, $total_vol_irrigation_production_previous_season_seed, $total_vol_irrigation_production_previous_season_cuttings, $total_vol_irrigation_production_previous_season_produce;
    public $total_irrigation_production_value_previous_season_total, $total_irrigation_production_value_previous_season_date_of_maximum_sales, $total_irrigation_production_value_previous_season_rate = 0, $total_irrigation_production_value_previous_season_value = 0;
    public $total_irrigation_production_value_previous_season_cuttings_value, $total_irrigation_production_value_previous_season_produce_value, $total_irrigation_production_value_previous_season_seed_value;
    public $total_irrigation_production_value_previous_season_seed_bundle, $total_irrigation_production_value_previous_season_seed_prevailing_price, $total_irrigation_production_value_previous_season_cuttings_prevailing_price, $total_irrigation_production_value_previous_season_produce_prevailing_price;
    public $bundle_multiplier_irrigation = 4;

    // Market & Certifications
    public $is_registered_seed_producer = false, $uses_certified_seed = false, $has_rtc_market_contract = false;
    public $sells_to_domestic_markets = false, $sells_to_international_markets = false;
    public $uses_market_information_systems = false, $sells_to_aggregation_centers = false;
    public $total_vol_aggregation_center_sales;
    public $market_segment = [];

    // Dynamic Array Properties
    public $area_under_cultivation = [];
    public $area_under_basic_seed_multiplication = [];
    public $area_under_certified_seed_multiplication = [];
    public $seed_service_unit_registration_details = [];
    public $market_information_systems = [];
    public $aggregation_center_sales = [];
    public $registrations = [['variety' => null, 'reg_date' => null, 'reg_no' => null]];

    public $inputOne = [[
        'conc_date_recorded' => null,
        'conc_partner_name' => null,
        'conc_country' => null,
        'conc_date_of_maximum_sale' => null,
        'conc_product_type' => 'Seed',
        'conc_volume_sold_previous_period' => null,
        'conc_financial_value_of_sales' => null,
    ]];
    public $inputTwo = [[
        'dom_date_recorded' => null,
        'dom_crop_type' => 'Cassava',
        'dom_market_name' => null,
        'dom_district' => null,
        'dom_date_of_maximum_sale' => null,
        'dom_product_type' => 'Seed',
        'dom_volume_sold_previous_period' => null,
        'dom_financial_value_of_sales' => null,
    ]];
    public $inputThree = [[
        'inter_date_recorded' => null,
        'inter_crop_type' => 'Cassava',
        'inter_market_name' => null,
        'inter_country' => 'Malawi',
        'inter_date_of_maximum_sale' => null,
        'inter_product_type' => 'Seed',
        'inter_volume_sold_previous_period' => null,
        'inter_financial_value_of_sales' => null,
    ]];

    public function rules()
    {
        return [
            'location_data.group_name' => 'required',
            'location_data.enterprise' => 'required',
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.section' => 'nullable',
            'date_of_followup' => 'required|date',
            'area_under_cultivation.*.variety' => 'nullable|distinct',
            'area_under_cultivation.*.area' => 'nullable|numeric',
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
            'total_production_value_previous_season_total' => 'required|numeric',
            'total_vol_irrigation_production_previous_season' => 'required|numeric',
            'total_vol_irrigation_production_previous_season_produce' => 'required|numeric',
            'total_vol_irrigation_production_previous_season_seed' => 'required|numeric',
            'total_vol_irrigation_production_previous_season_cuttings' => 'required|numeric',
            'total_irrigation_production_value_previous_season_total' => 'required_unless:location_data.enterprise,Cassava',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
            'registrations.*.variety' => 'required_if_accepted:is_registered_seed_producer',
            'registrations.*.reg_date' => 'required_if_accepted:is_registered_seed_producer',
            'registrations.*.reg_no' => 'required_if_accepted:is_registered_seed_producer',
        ];
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'district',
            'location_data.epa' => 'epa',
            'location_data.enterprise' => 'enterprise',
            'location_data.group_name' => 'group name',
            'is_registered' => 'formally registered entity',
            'is_registered_seed_producer' => 'registered seed producer',
            'aggregation_center_sales.*.name' => 'aggregation center sales name',
            'total_vol_aggregation_center_sales' => 'total aggregation center sales previous season',
            'market_information_systems.*.name' => 'market information systems name',
            'uses_market_information_systems' => 'sell your products through market information systems',
            'area_under_cultivation.*.variety' => 'area under cultivation (variety)',
            'area_under_cultivation.*.area' => 'area under cultivation (area)',
            'area_under_basic_seed_multiplication.*.variety' => 'area under basic seed multiplication (variety)',
            'area_under_basic_seed_multiplication.*.area' => 'area under basic seed multiplication (area)',
            'area_under_certified_seed_multiplication.*.variety' => 'area under certified seed multiplication (variety)',
            'area_under_certified_seed_multiplication.*.area' => 'area under certified seed multiplication (area)',
            'number_of_plantlets_produced.*' => 'number of plantlets produced',
            'number_of_screen_house_vines_harvested' => 'number of screen house vines harvested',
            'number_of_screen_house_min_tubers_harvested' => 'number of screen house mini tubers harvested',
            'number_of_sah_plants_produced' => 'number of sah plants produced',
            'registrations.*.variety' => 'registration (variety)',
            'registrations.*.reg_date' => 'registration (date)',
            'registrations.*.reg_no' => 'registration (number)',

        ];
    }

    public function mount($id, $uuid)
    {
        $farmer = RtcProductionFarmer::where('uuid', $uuid)->where('id', $id)->firstOrFail();

        $this->openSubmission = true;
        $this->routePrefix = Route::current()->getPrefix();
        $this->editData($farmer);

    }

    #[On('update-form')]
    public function updateFormListener()
    {
        return; // Retained placeholder for frontend event binding
    }

    public function updatedSellsToAggregationCenters($value)
    {
        if (!$value) $this->total_vol_aggregation_center_sales = 0;
    }

    // --- GENERIC ARRAY HANDLERS ---

    private function addToArray(string $property, array $template)
    {
        $this->{$property}[] = $template;
    }

    private function removeFromArray(string $property, int $index)
    {
        unset($this->{$property}[$index]);
        $this->{$property} = array_values($this->{$property}); // Reindex
    }

    // --- SPECIFIC ARRAY ACTIONS ---

    public function addRegistration()
    {
        $this->addToArray('registrations', ['variety' => '', 'reg_date' => '', 'reg_no' => '']);
    }
    public function removeRegistration($index)
    {
        $this->removeFromArray('registrations', $index);
    }

    public function addInputOne()
    {
        $this->addToArray('inputOne', [
            'conc_date_recorded' => null,
            'conc_partner_name' => null,
            'conc_country' => 'Malawi',
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => 'Seed',
            'conc_volume_sold_previous_period' => null,
            'conc_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputOne($index)
    {
        $this->removeFromArray('inputOne', $index);
    }

    public function addInputTwo()
    {
        $this->addToArray('inputTwo', [
            'dom_date_recorded' => null,
            'dom_crop_type' => 'Cassava',
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => 'Seed',
            'dom_volume_sold_previous_period' => null,
            'dom_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputTwo($index)
    {
        $this->removeFromArray('inputTwo', $index);
    }

    public function addInputThree()
    {
        $this->addToArray('inputThree', [
            'inter_date_recorded' => null,
            'inter_crop_type' => 'Cassava',
            'inter_market_name' => null,
            'inter_country' => 'Malawi',
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => 'Seed',
            'inter_volume_sold_previous_period' => null,
            'inter_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputThree($index)
    {
        $this->removeFromArray('inputThree', $index);
    }

    public function addAreaofCultivation()
    {
        $this->addToArray('area_under_cultivation', ['variety' => null, 'area' => null]);
    }
    public function removeAreaofCultivation($index)
    {
        $this->removeFromArray('area_under_cultivation', $index);
    }

    public function addBasicSeed()
    {
        $this->addToArray('area_under_basic_seed_multiplication', ['variety' => null, 'area' => null]);
    }
    public function removeBasicSeed($index)
    {
        $this->removeFromArray('area_under_basic_seed_multiplication', $index);
    }

    public function addCertifiedSeed()
    {
        $this->addToArray('area_under_certified_seed_multiplication', ['variety' => null, 'area' => null]);
    }
    public function removeCertifiedSeed($index)
    {
        $this->removeFromArray('area_under_certified_seed_multiplication', $index);
    }

    public function addSales()
    {
        $this->addToArray('aggregation_center_sales', ['name' => null]);
    }
    public function removeSales($index)
    {
        $this->removeFromArray('aggregation_center_sales', $index);
    }

    public function addMIS()
    {
        $this->addToArray('market_information_systems', ['name' => null]);
    }
    public function removeMIS($index)
    {
        $this->removeFromArray('market_information_systems', $index);
    }


    // --- VALIDATION ---

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
                'inputOne.*.conc_date_recorded' => 'contract agreement date recorded',
                'inputOne.*.conc_partner_name' => 'contract agreement partner name',
                'inputOne.*.conc_country' => 'contract agreement country',
                'inputOne.*.conc_date_of_maximum_sale' => 'contract agreement date of maximum sale',
                'inputOne.*.conc_product_type' => 'contract agreement product type',
                'inputOne.*.conc_volume_sold_previous_period' => 'contract agreement volume sold previous period',
                'inputOne.*.conc_financial_value_of_sales' => 'contract agreement financial value of sales',
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

                throw $e;
            }
        }
    }

    // --- EXCHANGE RATE CALCULATIONS ---

    public function updatedDateOfFollowUp($value)
    {
        $this->total_production_value_previous_season_date_of_maximum_sales = $value;
        $this->total_irrigation_production_value_previous_season_date_of_maximum_sales = $value;
    }

    #[On('date-change')]
    public function realTimeDateOfFollowUp()
    {
        // Reactivity hook
    }

    public function exchangeRateCalculateProduction()
    {
        $this->processExchangeRate('total_production_value_previous_season', $this->total_production_value_previous_season_value ?? null, $this->total_production_value_previous_season_date_of_maximum_sales ?? null);
    }

    public function exchangeRateCalculateIrrigation()
    {
        $this->processExchangeRate('total_irrigation_production_value_previous_season', $this->total_irrigation_production_value_previous_season_value ?? null, $this->total_production_value_previous_season_date_of_maximum_sales ?? null);
    }

    protected function processExchangeRate($key, $value, $date)
    {
        if ($value && $date) {
            $rate = (new ExchangeRateHelper())->getRate($value, $date);

            if ($rate === null) {
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = 0;
                    $this->total_production_value_previous_season_total = 0;
                } else {
                    $this->total_irrigation_production_value_previous_season_rate = 0;
                    $this->total_irrigation_production_value_previous_season_total = 0;
                }
            } else {
                $totalValue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = $rate;
                    $this->total_production_value_previous_season_total = $totalValue;
                } else {
                    $this->total_irrigation_production_value_previous_season_rate = $rate;
                    $this->total_irrigation_production_value_previous_season_total = $totalValue;
                }
            }
        }
    }

    // --- SAVING AND HYDRATION ---

    public function save()
    {
        try {
            $this->validate();
            $this->validateDynamicForms();
        } catch (Throwable $e) {
            $this->dispatch('show-alert', data: ['type' => 'error', 'message' => 'There are errors in the form.']);
            throw $e;
        }

        DB::beginTransaction();
        try {
            $recruit = RtcProductionFarmer::where('pf_id', $this->uniqueId)->firstOrFail();

            // 1. Update Core Data
            $recruit->update($this->prepareFarmerData());

            // 2. Sync Relationships (Erase & Recreate)
            $this->syncRelations($recruit);

            // 3. Insert specific market tables
            $this->addMoreData($recruit);

            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully updated! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-farmers/view/' . $recruit->pf_id . '">View Submission here</a>',
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            $this->dispatch('show-alert', data: ['type' => 'error', 'message' => 'Something went wrong!']);
            Log::error($th->getMessage());
        }
    }

    private function prepareFarmerData(): array
    {
        $segment = collect($this->market_segment);

        return [
            'epa' => $this->location_data['epa'],
            'district' => $this->location_data['district'],
            'section' => $this->location_data['section'],
            'enterprise' => $this->location_data['enterprise'],
            'group_name' => $this->location_data['group_name'],
            'group' => $this->group,
            'category' => $this->category,
            'sector' => $this->sector,
            'date_of_followup' => $this->date_of_followup,

            // Production
            'number_of_plantlets_produced_cassava' => $this->number_of_plantlets_produced['cassava'] ?? 0,
            'number_of_plantlets_produced_potato' => $this->number_of_plantlets_produced['potato'] ?? 0,
            'number_of_plantlets_produced_sweet_potato' => $this->number_of_plantlets_produced['sweet_potato'] ?? 0,
            'number_of_screen_house_vines_harvested' => $this->number_of_screen_house_vines_harvested ?? 0,
            'number_of_screen_house_min_tubers_harvested' => $this->number_of_screen_house_min_tubers_harvested ?? 0,
            'number_of_sah_plants_produced' => $this->number_of_sah_plants_produced ?? 0,

            // Certifications
            'is_registered_seed_producer' => $this->is_registered_seed_producer,
            'uses_certified_seed' => $this->uses_certified_seed,
            'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
            'market_segment_processed' => $segment->contains('Processed') ? 1 : 0,
            'market_segment_seed' => $segment->contains('Seed') ? 1 : 0,
            'market_segment_cuttings' => $segment->contains('Cuttings') ? 1 : 0,
            'has_rtc_market_contract' => $this->has_rtc_market_contract,

            // Values & Volumes Rainfed
            'total_vol_production_previous_season' => $this->total_vol_production_previous_season ?? 0,
            'total_vol_production_previous_season_produce' => $this->total_vol_production_previous_season_produce ?? 0,
            'total_vol_production_previous_season_seed' => $this->total_vol_production_previous_season_seed ?? 0,
            'total_vol_production_previous_season_cuttings' => $this->total_vol_production_previous_season_cuttings ?? 0,
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

            // Values & Volumes Irrigation
            'total_vol_irrigation_production_previous_season' => $this->total_vol_irrigation_production_previous_season ?? 0,
            'total_vol_irrigation_production_previous_season_produce' => $this->total_vol_irrigation_production_previous_season_produce ?? 0,
            'total_vol_irrigation_production_previous_season_seed' => $this->total_vol_irrigation_production_previous_season_seed ?? 0,
            'total_vol_irrigation_production_previous_season_cuttings' => $this->total_vol_irrigation_production_previous_season_cuttings ?? 0,
            'irr_prod_value_previous_season_total' => $this->total_irrigation_production_value_previous_season_value ?? 0,
            'irr_prod_value_previous_season_produce' => $this->total_irrigation_production_value_previous_season_produce_value ?? 0,
            'irr_prod_value_previous_season_seed' => $this->total_irrigation_production_value_previous_season_seed_value ?? 0,
            'irr_prod_value_previous_season_cuttings' => $this->total_irrigation_production_value_previous_season_cuttings_value ?? 0,
            'irr_prod_value_produce_prevailing_price' => $this->total_irrigation_production_value_previous_season_cuttings_prevailing_price ?? 0,
            'irr_prod_value_seed_prevailing_price' => $this->total_irrigation_production_value_previous_season_produce_prevailing_price ?? 0,
            'irr_prod_value_cuttings_prevailing_price' => $this->total_irrigation_production_value_previous_season_seed_prevailing_price ?? 0,
            'irr_prod_value_previous_season_date_of_max_sales' => $this->total_irrigation_production_value_previous_season_date_of_maximum_sales,
            'irr_prod_value_previous_season_usd_rate' => $this->total_irrigation_production_value_previous_season_rate ?? 0,
            'irr_prod_value_previous_season_usd_value' => $this->total_irrigation_production_value_previous_season_total ?? 0,

            // Market Access
            'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
            'sells_to_international_markets' => $this->sells_to_international_markets,
            'uses_market_information_systems' => $this->uses_market_information_systems,
            'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
            'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales,

            // Hardcoded defaults mapping from original
            'total_vol_production_previous_season_seed_bundle' => 0,
            'total_vol_irrigation_production_previous_season_seed_bundle' => 0,
            'prod_value_previous_season_seed_bundle' => 0,
            'irr_prod_value_previous_season_seed_bundle' => 0,
        ];
    }

    private function syncRelations(RtcProductionFarmer $farmer)
    {
        // Clear existing data
        if (count($this->area_under_cultivation) > 0) $farmer->cultivatedArea()->delete();
        if (count($this->area_under_basic_seed_multiplication) > 0) $farmer->basicSeed()->delete();
        if (count($this->area_under_certified_seed_multiplication) > 0) $farmer->certifiedSeed()->delete();
        if (count($this->market_information_systems) > 0) $farmer->marketInformationSystems()->delete();
        if (count($this->aggregation_center_sales) > 0) $farmer->aggregationCenters()->delete();
        if ($this->is_registered_seed_producer) $farmer->registeredSeed()->delete();

        // Agreement Markets
        if (count($this->inputOne) > 0) $farmer->agreements()->delete();
        if (count($this->inputTwo) > 0) $farmer->doms()->delete();
        if (count($this->inputThree) > 0) $farmer->intermarkets()->delete();

        // Bulk Create
        if (!empty($this->area_under_cultivation)) {
            $farmer->cultivatedArea()->createMany($this->area_under_cultivation);
        }

        $validBasic = array_filter($this->area_under_basic_seed_multiplication, fn($item) => $item['area'] !== null || $item['variety'] !== null);
        if (!empty($validBasic)) {
            $farmer->basicSeed()->createMany($validBasic);
        }

        $validCert = array_filter($this->area_under_certified_seed_multiplication, fn($item) => $item['area'] !== null || $item['variety'] !== null);
        if (!empty($validCert)) {
            $farmer->certifiedSeed()->createMany($validCert);
        }

        $validMis = array_filter($this->market_information_systems, fn($item) => $item['name'] !== null);
        if (!empty($validMis)) {
            $farmer->marketInformationSystems()->createMany($validMis);
        }

        if ($this->sells_to_aggregation_centers) {
            $validSales = array_filter($this->aggregation_center_sales, fn($item) => $item['name'] !== null);
            if (!empty($validSales)) $farmer->aggregationCenters()->createMany($validSales);
        }

        if ($this->is_registered_seed_producer) {
            $validReg = array_filter($this->registrations, fn($item) => isset($item['reg_name']) && $item['reg_name'] !== null);
            if (!empty($validReg)) $farmer->registeredSeed()->createMany($validReg);
        }
    }

    public function addMoreData($recruit)
    {
        $dates = ['created_at' => now(), 'updated_at' => now(), 'status' => 'approved'];

        if ($this->has_rtc_market_contract && count($this->inputOne) > 0) {
            $data = array_map(fn($input) => [
                'rpm_farmer_id' => $recruit->id,
                'date_recorded' => $input['conc_date_recorded'] ?? now(),
                'partner_name' => $input['conc_partner_name'],
                'country' => $input['conc_country'],
                'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                'product_type' => $input['conc_product_type'],
                'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['conc_financial_value_of_sales'],
            ] + $dates, $this->inputOne);
            RpmFarmerConcAgreement::insert($data);
        }

        if ($this->sells_to_domestic_markets && count($this->inputTwo) > 0) {
            $data = array_map(fn($input) => [
                'rpm_farmer_id' => $recruit->id,
                'date_recorded' => $input['dom_date_recorded'] ?? now(),
                'crop_type' => $input['dom_crop_type'],
                'market_name' => $input['dom_market_name'],
                'district' => $input['dom_district'],
                'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                'product_type' => $input['dom_product_type'],
                'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
            ] + $dates, $this->inputTwo);
            RpmFarmerDomMarket::insert($data);
        }

        if ($this->sells_to_international_markets && count($this->inputThree) > 0) {
            $data = array_map(fn($input) => [
                'rpm_farmer_id' => $recruit->id,
                'date_recorded' => $input['inter_date_recorded'] ?? now(),
                'crop_type' => $input['inter_crop_type'],
                'market_name' => $input['inter_market_name'],
                'country' => $input['inter_country'],
                'date_of_maximum_sale' => $input['inter_date_of_maximum_sale'],
                'product_type' => $input['inter_product_type'],
                'volume_sold_previous_period' => $input['inter_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['inter_financial_value_of_sales'],
            ] + $dates, $this->inputThree);
            RpmFarmerInterMarket::insert($data);
        }
    }

    public function editData($data)
    {
        $marketSegment = collect([
            'Fresh' => $data->market_segment_fresh,
            'Processed' => $data->market_segment_processed,
            'Seed' => $data->market_segment_seed,
            'Cuttings' => $data->market_segment_cuttings,
        ])->filter(fn($value) => $value == 1)->keys()->toArray();

        $districts = DistrictObject::districts();
        $districtNameFromData = strtolower($data->district ?? '');

        $matchedDistrict = collect($districts)->first(function ($officialName) use ($districtNameFromData) {
            $officialNameLower = strtolower($officialName);
            return $officialNameLower === $districtNameFromData || str_contains($districtNameFromData, $officialNameLower) || str_contains($officialNameLower, $districtNameFromData);
        });


        $this->fill([
            'uniqueId' => $data->pf_id,
            'location_data' => [
                'epa' => $data->epa,
                'district' => trim($matchedDistrict) ?? trim($data->district),
                'section' => $data->section,
                'enterprise' => $data->enterprise,
                'group_name' => $data->group_name,
            ],
            'group' => $data->group,
            'category' => $data->category,
            'sector' => $data->sector,
            'date_of_followup' => $data->date_of_followup ? Carbon::parse($data->date_of_followup)->format('Y-m-d') : null,
            'number_of_plantlets_produced' => [
                'cassava' => $data->number_of_plantlets_produced_cassava ?? 0,
                'potato' => $data->number_of_plantlets_produced_potato ?? 0,
                'sweet_potato' => $data->number_of_plantlets_produced_sweet_potato ?? 0,
            ],
            'number_of_screen_house_vines_harvested' => $data->number_of_screen_house_vines_harvested ?? 0,
            'number_of_screen_house_min_tubers_harvested' => $data->number_of_screen_house_min_tubers_harvested ?? 0,
            'number_of_sah_plants_produced' => $data->number_of_sah_plants_produced ?? 0,
            'is_registered_seed_producer' => $data->is_registered_seed_producer,
            'uses_certified_seed' => $data->uses_certified_seed,
            'market_segment' => $marketSegment,
            'has_rtc_market_contract' => (bool) $data->has_rtc_market_contract,

            // Production Rainfed
            'total_vol_production_previous_season' => $data->total_vol_production_previous_season ?? 0,
            'total_vol_production_previous_season_produce' => $data->total_vol_production_previous_season_produce ?? 0,
            'total_vol_production_previous_season_seed' => $data->total_vol_production_previous_season_seed ?? 0,
            'total_vol_production_previous_season_cuttings' => $data->total_vol_production_previous_season_cuttings ?? 0,
            'total_production_value_previous_season_value' => $data->prod_value_previous_season_total ?? 0,
            'total_production_value_previous_season_produce_value' => $data->prod_value_previous_season_produce ?? 0,
            'total_production_value_previous_season_seed_value' => $data->prod_value_previous_season_seed ?? 0,
            'total_production_value_previous_season_cuttings_value' => $data->prod_value_previous_season_cuttings ?? 0,
            'total_production_value_previous_season_cuttings_prevailing_price' => $data->prod_value_produce_prevailing_price ?? 0,
            'total_production_value_previous_season_produce_prevailing_price' => $data->prod_value_seed_prevailing_price ?? 0,
            'total_production_value_previous_season_seed_prevailing_price' => $data->prod_value_cuttings_prevailing_price ?? 0,
            'total_production_value_previous_season_date_of_maximum_sales' => $data->prod_value_previous_season_date_of_max_sales,
            'total_production_value_previous_season_rate' => $data->prod_value_previous_season_usd_rate,
            'total_production_value_previous_season_total' => $data->prod_value_previous_season_usd_value,

            // Production Irrigation
            'total_vol_irrigation_production_previous_season' => $data->total_vol_irrigation_production_previous_season ?? 0,
            'total_vol_irrigation_production_previous_season_produce' => $data->total_vol_irrigation_production_previous_season_produce ?? 0,
            'total_vol_irrigation_production_previous_season_seed' => $data->total_vol_irrigation_production_previous_season_seed ?? 0,
            'total_vol_irrigation_production_previous_season_cuttings' => $data->total_vol_irrigation_production_previous_season_cuttings ?? 0,
            'total_irrigation_production_value_previous_season_value' => $data->irr_prod_value_previous_season_total,
            'total_irrigation_production_value_previous_season_produce_value' => $data->irr_prod_value_previous_season_produce,
            'total_irrigation_production_value_previous_season_seed_value' => $data->irr_prod_value_previous_season_seed,
            'total_irrigation_production_value_previous_season_cuttings_value' => $data->irr_prod_value_previous_season_cuttings,
            'total_irrigation_production_value_previous_season_cuttings_prevailing_price' => $data->irr_prod_value_produce_prevailing_price,
            'total_irrigation_production_value_previous_season_produce_prevailing_price' => $data->irr_prod_value_seed_prevailing_price,
            'total_irrigation_production_value_previous_season_seed_prevailing_price' => $data->irr_prod_value_cuttings_prevailing_price,
            'total_irrigation_production_value_previous_season_date_of_maximum_sales' => $data->irr_prod_value_previous_season_date_of_max_sales,
            'total_irrigation_production_value_previous_season_rate' => $data->irr_prod_value_previous_season_usd_rate,
            'total_irrigation_production_value_previous_season_total' => $data->irr_prod_value_previous_season_usd_value,

            // Markets
            'sells_to_domestic_markets' => $data->sells_to_domestic_markets ?? false,
            'sells_to_international_markets' => $data->sells_to_international_markets ?? false,
            'uses_market_information_systems' => $data->uses_market_information_systems ?? false,
            'sells_to_aggregation_centers' => $data->sells_to_aggregation_centers ?? false,
            'total_vol_aggregation_center_sales' => $data->total_vol_aggregation_center_sales ?? 0,
        ]);

        $this->hydrateRelations($data);
    }

    private function hydrateRelations($data)
    {
        if ($data->cultivatedArea()->exists()) {
            $this->area_under_cultivation = $data->cultivatedArea()->get(['variety', 'area'])->toArray();
        }

        if ($data->basicSeed()->exists()) {
            $this->area_under_basic_seed_multiplication = $data->basicSeed()->get(['variety', 'area'])->toArray();
        }

        if ($data->certifiedSeed()->exists()) {
            $this->area_under_certified_seed_multiplication = $data->certifiedSeed()->get(['variety', 'area'])->toArray();
        }

        if ($data->marketInformationSystems()->exists()) {
            $this->market_information_systems = $data->marketInformationSystems()->get(['name'])->toArray();
        }

        if ($data->aggregationCenters()->exists()) {
            $this->aggregation_center_sales = $data->aggregationCenters()->get(['name'])->toArray();
        }

        if ($data->registeredSeed()->exists()) {
            $this->registrations = $data->registeredSeed()->get(['variety', 'reg_date', 'reg_number as reg_no'])->toArray();
        }

        if ($data->agreements()->exists()) {
            $this->inputOne = $data->agreements()->get()->map(fn($item) => [
                'conc_date_recorded' => $item->date_recorded,
                'conc_partner_name' => $item->partner_name,
                'conc_country' => $item->country,
                'conc_date_of_maximum_sale' => $item->date_of_maximum_sale,
                'conc_product_type' => $item->product_type,
                'conc_volume_sold_previous_period' => $item->volume_sold_previous_period,
                'conc_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }

        if ($data->doms()->exists()) {
            $this->inputTwo = $data->doms()->get()->map(fn($item) => [
                'dom_date_recorded' => $item->date_recorded,
                'dom_crop_type' => $item->crop_type,
                'dom_market_name' => $item->market_name,
                'dom_district' => trim($item->district),
                'dom_date_of_maximum_sale' => $item->date_of_maximum_sale,
                'dom_product_type' => $item->product_type,
                'dom_volume_sold_previous_period' => $item->volume_sold_previous_period,
                'dom_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }

        if ($data->intermarkets()->exists()) {
            $this->inputThree = $data->intermarkets()->get()->map(fn($item) => [
                'inter_date_recorded' => $item->date_recorded,
                'inter_crop_type' => $item->crop_type,
                'inter_market_name' => $item->market_name,
                'inter_country' => $item->country,
                'inter_date_of_maximum_sale' => $item->date_of_maximum_sale,
                'inter_product_type' => $item->product_type,
                'inter_volume_sold_previous_period' => $item->volume_sold_previous_period,
                'inter_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }
    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-production-farmers.edit');
    }
}
