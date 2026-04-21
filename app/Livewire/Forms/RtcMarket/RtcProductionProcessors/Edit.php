<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManualDataTrait;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExchangeRateHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Helpers\DistrictObject;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $form_name = 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS';

    // Core Properties
    public $uuid;
    public $uniqueId;
    public $routePrefix;
    public $openSubmission = false;
    
    // Component State
    public $selectedIndicator, $submissionPeriodId, $selectedForm, $selectedFinancialYear, $selectedMonth;
    public $targetSet = false, $targetIds = [];
    public $rate = 0;

    // Processor & Location Data
    public $location_data = ['enterprise' => 'Cassava', 'district' => null, 'epa' => null, 'section' => null, 'group_name' => null];
    public $date_of_recruitment, $name_of_actor, $name_of_representative, $phone_number, $type, $approach, $sector, $group, $category;
    public $date_of_followup;

    // Production & Pricing Data
    public $area_under_cultivation = [['variety' => null, 'area' => null]];
    public $total_vol_production_previous_season, $total_vol_production_previous_season_seed, $total_vol_production_previous_season_cuttings, $total_vol_production_previous_season_produce;
    public $total_production_value_previous_season_total, $total_production_value_previous_season_date_of_maximum_sales, $total_production_value_previous_season_rate = 0, $total_production_value_previous_season_value = 0;
    public $total_production_value_previous_season_cuttings_value, $total_production_value_previous_season_produce_value, $total_production_value_previous_season_seed_value;
    public $total_production_value_previous_season_seed_bundle, $total_production_value_previous_season_seed_prevailing_price, $total_production_value_previous_season_cuttings_prevailing_price, $total_production_value_previous_season_produce_prevailing_price;
    public $bundle_multiplier = 4;

    // Markets & Agreements
    public $market_segment = [];
    public $has_rtc_market_contract = false;
    public $sells_to_domestic_markets = false;
    public $sells_to_international_markets = false;
    public $uses_market_information_systems = false;
    public $sells_to_aggregation_centers = false;
    public $total_vol_aggregation_center_sales;

    // Dynamic Arrays
    public $market_information_systems = [];
    public $aggregation_center_sales = [];
    
    public $inputOne = [[
        'conc_date_recorded' => null, 'conc_partner_name' => null, 'conc_country' => 'Malawi',
        'conc_date_of_maximum_sale' => null, 'conc_product_type' => 'Seed',
        'conc_volume_sold_previous_period' => null, 'conc_financial_value_of_sales' => null,
    ]];

    public $inputTwo = [[
        'dom_date_recorded' => null, 'dom_crop_type' => 'Cassava', 'dom_market_name' => null,
        'dom_district' => null, 'dom_date_of_maximum_sale' => null, 'dom_product_type' => 'Seed',
        'dom_volume_sold_previous_period' => null, 'dom_financial_value_of_sales' => null,
    ]];

    public $inputThree = [[
        'inter_date_recorded' => null, 'inter_crop_type' => 'Cassava', 'inter_market_name' => null,
        'inter_country' => 'Malawi', 'inter_date_of_maximum_sale' => null, 'inter_product_type' => 'Seed',
        'inter_volume_sold_previous_period' => null, 'inter_financial_value_of_sales' => null,
    ]];

    public function rules()
    {
        return [
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.enterprise' => 'required',
            'location_data.section' => 'required',
            'location_data.group_name' => 'required',
            'date_of_followup' => 'required|date',
            'market_segment' => 'required',
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
            'total_vol_production_previous_season' => 'required|numeric',
            'total_vol_production_previous_season_produce' => 'required|numeric',
            'total_vol_production_previous_season_seed' => 'required|numeric',
            'total_vol_production_previous_season_cuttings' => 'required|numeric',
            'total_production_value_previous_season_total' => 'required|numeric',
        ];
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'District',
            'location_data.epa' => 'EPA',
            'location_data.enterprise' => 'Enterprise',
            'location_data.section' => 'Section',
            'location_data.group_name' => 'Group Name',
            'group' => 'required',
            'sector' => 'required',
            'category' => 'nullable',
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
        ];
    }

    public function mount($id, $uuid)
    {
        $processor = RtcProductionProcessor::where('uuid', $uuid)->where('id', $id)->firstOrFail();
        $this->openSubmission = true;
        $this->routePrefix = Route::current()->getPrefix();
        $this->editData($processor);
    }

    // --- GENERIC ARRAY HANDLERS ---

    private function addToArray(string $property, array $template)
    {
        $this->{$property}[] = $template;
    }

    private function removeFromArray(string $property, int $index)
    {
        unset($this->{$property}[$index]);
        $this->{$property} = array_values($this->{$property}); // Reindex to prevent gaps
    }

    // --- SPECIFIC ARRAY ACTIONS ---

    public function addInputOne() {
        $this->addToArray('inputOne', [
            'conc_date_recorded' => null, 'conc_partner_name' => null, 'conc_country' => 'Malawi',
            'conc_date_of_maximum_sale' => null, 'conc_product_type' => 'Seed',
            'conc_volume_sold_previous_period' => null, 'conc_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputOne($index) { $this->removeFromArray('inputOne', $index); }

    public function addInputTwo() {
        $this->addToArray('inputTwo', [
            'dom_date_recorded' => null, 'dom_crop_type' => 'Cassava', 'dom_market_name' => null,
            'dom_district' => null, 'dom_date_of_maximum_sale' => null, 'dom_product_type' => 'Seed',
            'dom_volume_sold_previous_period' => null, 'dom_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputTwo($index) { $this->removeFromArray('inputTwo', $index); }

    public function addInputThree() {
        $this->addToArray('inputThree', [
            'inter_date_recorded' => null, 'inter_crop_type' => 'Cassava', 'inter_market_name' => null,
            'inter_country' => 'Malawi', 'inter_date_of_maximum_sale' => null, 'inter_product_type' => 'Seed',
            'inter_volume_sold_previous_period' => null, 'inter_financial_value_of_sales' => null,
        ]);
    }
    public function removeInputThree($index) { $this->removeFromArray('inputThree', $index); }

    public function addSales() { $this->addToArray('aggregation_center_sales', ['name' => null]); }
    public function removeSales($index) { $this->removeFromArray('aggregation_center_sales', $index); }

    public function addMIS() { $this->addToArray('market_information_systems', ['name' => null]); }
    public function removeMIS($index) { $this->removeFromArray('market_information_systems', $index); }

    public function addAreaofCultivation() { $this->addToArray('area_under_cultivation', ['variety' => null, 'area' => null]); }
    public function removeAreaofCultivation($index) { $this->removeFromArray('area_under_cultivation', $index); }

    public function resetValues($name)
    {
        $this->reset($name);
    }

    public function resetall()
    {
        $this->reset();
        $this->addInputOne();
        $this->addInputTwo();
        $this->addInputThree();
    }

    // --- VALIDATION ---

    public function validateDynamicForms()
    {
        $rules = [];
        $attributes = [];

        if ($this->sells_to_domestic_markets) {
            $rules = array_merge($rules, [
                'inputTwo.*.dom_date_recorded' => 'required', 'inputTwo.*.dom_crop_type' => 'required',
                'inputTwo.*.dom_market_name' => 'required', 'inputTwo.*.dom_district' => 'required',
                'inputTwo.*.dom_date_of_maximum_sale' => 'required', 'inputTwo.*.dom_product_type' => 'required',
                'inputTwo.*.dom_volume_sold_previous_period' => 'required', 'inputTwo.*.dom_financial_value_of_sales' => 'required',
            ]);
            $attributes = array_merge($attributes, [
                'inputTwo.*.dom_date_recorded' => 'date recorded', 'inputTwo.*.dom_crop_type' => 'crop type',
                'inputTwo.*.dom_market_name' => 'market name', 'inputTwo.*.dom_district' => 'district',
                'inputTwo.*.dom_date_of_maximum_sale' => 'date of maximum sale', 'inputTwo.*.dom_product_type' => 'product type',
                'inputTwo.*.dom_volume_sold_previous_period' => 'volume sold previous period', 'inputTwo.*.dom_financial_value_of_sales' => 'financial value of sales',
            ]);
        }

        if ($this->sells_to_international_markets) {
            $rules = array_merge($rules, [
                'inputThree.*.inter_date_recorded' => 'required', 'inputThree.*.inter_crop_type' => 'required',
                'inputThree.*.inter_market_name' => 'required', 'inputThree.*.inter_country' => 'required',
                'inputThree.*.inter_date_of_maximum_sale' => 'required', 'inputThree.*.inter_product_type' => 'required',
                'inputThree.*.inter_volume_sold_previous_period' => 'required', 'inputThree.*.inter_financial_value_of_sales' => 'required',
            ]);
            $attributes = array_merge($attributes, [
                'inputThree.*.inter_date_recorded' => 'date recorded', 'inputThree.*.inter_crop_type' => 'crop type',
                'inputThree.*.inter_market_name' => 'market name', 'inputThree.*.inter_country' => 'country',
                'inputThree.*.inter_date_of_maximum_sale' => 'date of maximum sale', 'inputThree.*.inter_product_type' => 'product type',
                'inputThree.*.inter_volume_sold_previous_period' => 'volume sold previous period', 'inputThree.*.inter_financial_value_of_sales' => 'financial value of sales',
            ]);
        }

        if (!empty($rules)) {
            $this->validate($rules, [], $attributes);
        }
    }

    // --- EXCHANGE RATE CALCULATIONS ---

    public function updatedDateOfFollowUp($value)
    {
        $this->total_production_value_previous_season_date_of_maximum_sales = $value;
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

    protected function processExchangeRate($key, $value, $date)
    {
        if ($value && $date) {
            $rate = (new ExchangeRateHelper())->getRate($value, $date);

            if ($rate === null) {
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = 0;
                    $this->total_production_value_previous_season_total = 0;
                }
            } else {
                $totalValue = round(((float) ($value ?? 0)) / (float) $rate, 2);
                if ($key === 'total_production_value_previous_season') {
                    $this->total_production_value_previous_season_rate = $rate;
                    $this->total_production_value_previous_season_total = $totalValue;
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
            $processor = RtcProductionProcessor::where('pp_id', $this->uniqueId)->firstOrFail();
            
            // 1. Update Core Data
            $processor->update($this->prepareProcessorData());

            // 2. Sync Relationships (Erase & Recreate)
            $this->syncRelations($processor);

            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully updated! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-production-and-marketing-form-processors-and-traders/view/' . $processor->pp_id . '">View Submission here</a>',
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            $this->dispatch('show-alert', data: ['type' => 'error', 'message' => 'Something went wrong!']);
            Log::error($th->getMessage());
        }
    }

    private function prepareProcessorData(): array
    {
        $segment = collect($this->market_segment);

        return [
            'epa' => $this->location_data['epa'],
            'district' => $this->location_data['district'],
            'section' => $this->location_data['section'],
            'enterprise' => $this->location_data['enterprise'],
            'group_name' => $this->location_data['group_name'],
            'date_of_followup' => $this->date_of_followup,
            
            // Markets
            'market_segment_fresh' => $segment->contains('Fresh') ? 1 : 0,
            'market_segment_processed' => $segment->contains('Processed') ? 1 : 0,
            'has_rtc_market_contract' => $this->has_rtc_market_contract,
            'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
            'sells_to_international_markets' => $this->sells_to_international_markets,
            'uses_market_information_systems' => $this->uses_market_information_systems,
            'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
            'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales,

            // Production Volumes & Values
            'total_vol_production_previous_season' => $this->total_vol_production_previous_season,
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

            // Hardcoded mappings
            'total_vol_production_previous_season_seed_bundle' => 0,
            'prod_value_previous_season_seed_bundle' => 0,
        ];
    }

    private function syncRelations(RtcProductionProcessor $processor)
    {
        // 1. Clean Slate (Delete existings)
        if (count($this->market_information_systems) > 0) $processor->marketInformationSystems()->delete();
        if (count($this->aggregation_center_sales) > 0) $processor->aggregationCenters()->delete();
        if (count($this->inputOne) > 0) $processor->agreements()->delete();
        if (count($this->inputTwo) > 0) $processor->doms()->delete();
        if (count($this->inputThree) > 0) $processor->intermarkets()->delete();

        // 2. Re-create simple relations
        $validMis = array_filter($this->market_information_systems, fn($item) => $item['name'] !== null);
        if (!empty($validMis)) {
            $processor->marketInformationSystems()->createMany($validMis);
        }

        if ($this->sells_to_aggregation_centers) {
            $validSales = array_filter($this->aggregation_center_sales, fn($item) => $item['name'] !== null);
            if (!empty($validSales)) $processor->aggregationCenters()->createMany($validSales);
        }

        // 3. Re-create Market Contracts / Agreements
        $dates = ['created_at' => now(), 'updated_at' => now(), 'status' => 'approved'];

        if ($this->has_rtc_market_contract && !empty($this->inputOne)) {
            $data = array_map(fn($input) => [
                'rpm_processor_id' => $processor->id,
                'date_recorded' => $input['conc_date_recorded'] ?? now(),
                'partner_name' => $input['conc_partner_name'],
                'country' => $input['conc_country'],
                'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                'product_type' => $input['conc_product_type'],
                'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['conc_financial_value_of_sales'],
            ] + $dates, $this->inputOne);
            RpmProcessorConcAgreement::insert($data);
        }

        if ($this->sells_to_domestic_markets && !empty($this->inputTwo)) {
            $data = array_map(fn($input) => [
                'rpm_processor_id' => $processor->id,
                'date_recorded' => $input['dom_date_recorded'] ?? now(),
                'crop_type' => $input['dom_crop_type'],
                'market_name' => $input['dom_market_name'],
                'district' => $input['dom_district'],
                'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                'product_type' => $input['dom_product_type'],
                'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
            ] + $dates, $this->inputTwo);
            RpmProcessorDomMarket::insert($data);
        }

        if ($this->sells_to_international_markets && !empty($this->inputThree)) {
            $data = array_map(fn($input) => [
                'rpm_processor_id' => $processor->id,
                'date_recorded' => $input['inter_date_recorded'] ?? now(),
                'crop_type' => $input['inter_crop_type'],
                'market_name' => $input['inter_market_name'],
                'country' => $input['inter_country'],
                'date_of_maximum_sale' => $input['inter_date_of_maximum_sale'],
                'product_type' => $input['inter_product_type'],
                'volume_sold_previous_period' => $input['inter_volume_sold_previous_period'],
                'financial_value_of_sales' => $input['inter_financial_value_of_sales'],
            ] + $dates, $this->inputThree);
            RpmProcessorInterMarket::insert($data);
        }
    }

    public function editData($data)
    {
        $marketSegment = collect([
            'Fresh' => $data->market_segment_fresh,
            'Processed' => $data->market_segment_processed,
        ])->filter(fn($value) => $value == 1)->keys()->toArray();

        $districts = DistrictObject::districts();
        $districtNameFromData = strtolower($data->district ?? '');

        $matchedDistrict = collect($districts)->first(function ($officialName) use ($districtNameFromData) {
            $officialNameLower = strtolower($officialName);
            return $officialNameLower === $districtNameFromData || str_contains($districtNameFromData, $officialNameLower) || str_contains($officialNameLower, $districtNameFromData);
        });

        $this->fill([
            'uniqueId' => $data->pp_id,
            'location_data' => [
                'epa' => $data->epa,
                'district' => $matchedDistrict ?? $data->district,
                'section' => $data->section,
                'enterprise' => $data->enterprise,
                'group_name' => $data->group_name,
            ],
            'group' => $data->group,
            'category' => $data->category,
            'sector' => $data->sector,
            'date_of_followup' => $data->date_of_followup ? Carbon::parse($data->date_of_followup)->format('Y-m-d') : null,
            'market_segment' => $marketSegment,
            'has_rtc_market_contract' => (bool) $data->has_rtc_market_contract,
            
            // Production Volumes
            'total_vol_production_previous_season' => $data->total_vol_production_previous_season ?? 0,
            'total_vol_production_previous_season_produce' => $data->total_vol_production_previous_season_produce ?? 0,
            'total_vol_production_previous_season_seed' => $data->total_vol_production_previous_season_seed ?? 0,
            'total_vol_production_previous_season_cuttings' => $data->total_vol_production_previous_season_cuttings ?? 0,
            
            // Production Values
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
        if ($data->marketInformationSystems()->exists()) {
            $this->market_information_systems = $data->marketInformationSystems()->get(['name'])->toArray();
        }

        if ($data->aggregationCenters()->exists()) {
            $this->aggregation_center_sales = $data->aggregationCenters()->get(['name'])->toArray();
        }

        if ($data->agreements()->exists()) {
            $this->inputOne = $data->agreements()->get()->map(fn($item) => [
                'conc_date_recorded' => $item->date_recorded, 'conc_partner_name' => $item->partner_name,
                'conc_country' => $item->country, 'conc_date_of_maximum_sale' => $item->date_of_maximum_sale,
                'conc_product_type' => $item->product_type, 'conc_volume_sold_previous_period' => $item->volume_sold_previous_period,
                'conc_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }

        if ($data->doms()->exists()) {
            $this->inputTwo = $data->doms()->get()->map(fn($item) => [
                'dom_date_recorded' => $item->date_recorded, 'dom_crop_type' => $item->crop_type,
                'dom_market_name' => $item->market_name, 'dom_district' => $item->district,
                'dom_date_of_maximum_sale' => $item->date_of_maximum_sale, 'dom_product_type' => $item->product_type,
                'dom_volume_sold_previous_period' => $item->volume_sold_previous_period, 'dom_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }

        if ($data->intermarkets()->exists()) {
            $this->inputThree = $data->intermarkets()->get()->map(fn($item) => [
                'inter_date_recorded' => $item->date_recorded, 'inter_crop_type' => $item->crop_type,
                'inter_market_name' => $item->market_name, 'inter_country' => $item->country,
                'inter_date_of_maximum_sale' => $item->date_of_maximum_sale, 'inter_product_type' => $item->product_type,
                'inter_volume_sold_previous_period' => $item->volume_sold_previous_period, 'inter_financial_value_of_sales' => $item->financial_value_of_sales,
            ])->toArray();
        }
    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-production-processors.edit');
    }
}