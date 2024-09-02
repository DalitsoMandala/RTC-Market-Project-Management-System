<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use Throwable;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ExchangeRate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Models\RpmProcessorFollowUp;
use Illuminate\Support\Facades\Auth;
use App\Models\RpmProcessorDomMarket;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorFollowUp;

class AddFollowUp extends Component
{
    use LivewireAlert;

    public $location_data = [];
    public $date_of_follow_up;
    public $market_segment = [

    ];
    public $has_rtc_market_contract = false;
    public $total_vol_production_previous_season;
    public $total_production_value_previous_season = [
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
    public $rate;
    public function rules()
    {



        $rules = [

            'date_of_follow_up' => 'required|date',
            'market_segment' => 'required', // Multiple market segments (array of strings)
            'aggregation_center_sales.*.name' => 'required_if_accepted:sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales' => 'required|numeric',
            'market_information_systems.*.name' => 'required_if_accepted:uses_market_information_systems',
            'total_vol_production_previous_season' => 'required|numeric',
            'total_production_value_previous_season.value' => 'required|numeric',
            'total_production_value_previous_season.date_of_maximum_sales' => 'required|date',


        ];
        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'date_of_follow_up' => 'date of follow up',
            'aggregation_center_sales.*.name' => 'aggregation center sales name',
            'total_vol_aggregation_center_sales' => 'total aggregation center sales previous season',
            'market_information_systems.*.name' => 'market information systems',
            'uses_market_information_systems' => 'sell your products through market information systems',
            'total_vol_production_previous_season' => 'total volume of production previous season',
            'total_production_value_previous_season.value' => 'total value of production previous season',
            'total_production_value_previous_season.date_of_maximum_sales' => 'date of maximum sales of production previous season',
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


                $secondTable = [
                    'rpm_processor_id' => $this->recruit,
                    'location_data' => $this->location_data,
                    'date_of_follow_up' => $this->date_of_follow_up,
                    'market_segment' => $this->market_segment,
                    'has_rtc_market_contract' => $this->has_rtc_market_contract,
                    'total_vol_production_previous_season' => $this->total_vol_production_previous_season,
                    'total_production_value_previous_season' => $this->total_production_value_previous_season,
                    'sells_to_domestic_markets' => $this->sells_to_domestic_markets,
                    'sells_to_international_markets' => $this->sells_to_international_markets,
                    'uses_market_information_systems' => $this->uses_market_information_systems,
                    'market_information_systems' => $this->uses_market_information_systems ? $this->market_information_systems : null,
                    'sells_to_aggregation_centers' => $this->sells_to_aggregation_centers,
                    'aggregation_centers' => $this->sells_to_aggregation_centers ? $this->aggregation_center_sales : null, // Stores aggregation center details (array of objects with name and volume sold)
                    'total_vol_aggregation_center_sales' => $this->total_vol_aggregation_center_sales,// Previous season volume in metric tonnes
                    'user_id' => auth()->user()->id,
                    'status' => 'approved'

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
                Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());


            }

        } catch (Throwable $th) {
            dd($th);
            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());

        }
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
            'conc_country' => null,
            'conc_date_of_maximum_sale' => null,
            'conc_product_type' => 'SEED',
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
            'dom_crop_type' => 'CASSAVA',
            'dom_market_name' => null,
            'dom_district' => null,
            'dom_date_of_maximum_sale' => null,
            'dom_product_type' => 'SEED',
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
            'inter_crop_type' => 'CASSAVA',
            'inter_market_name' => null,
            'inter_country' => 'Malawi',
            'inter_date_of_maximum_sale' => null,
            'inter_product_type' => 'SEED',
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
        $dates = [
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 'approved'
        ];
        try {
            $thirdTable = array();

            foreach ($this->inputOne as $index => $input) {

                $thirdTable[] = [

                    'rpm_processor_id' => $recruit->id,
                    'date_recorded' => $input['conc_date_recorded'] ?? now(),
                    'partner_name' => strtoupper($input['conc_partner_name']),
                    'country' => $input['conc_country'],
                    'date_of_maximum_sale' => $input['conc_date_of_maximum_sale'],
                    'product_type' => $input['conc_product_type'],
                    'volume_sold_previous_period' => $input['conc_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['conc_financial_value_of_sales'],
                    ...$dates
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
                    'market_name' => strtoupper($input['dom_market_name']),
                    'district' => $input['dom_district'],
                    'date_of_maximum_sale' => $input['dom_date_of_maximum_sale'],
                    'product_type' => $input['dom_product_type'],
                    'volume_sold_previous_period' => $input['dom_volume_sold_previous_period'],
                    'financial_value_of_sales' => $input['dom_financial_value_of_sales'],
                    ...$dates
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
                    ...$dates
                ];
            }

            if ($this->sells_to_international_markets) {
                RpmProcessorInterMarket::insert($fifthTable);
            }


        } catch (UserErrorException $e) {
            # code...

            session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
        }

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
    public function updatedSelectedRecruit($id)
    {
        $recruit = RtcProductionProcessor::find($id);
        if ($recruit) {

            $this->location_data['epa'] = json_decode($recruit->location_data)->epa;
            $this->location_data['district'] = json_decode($recruit->location_data)->district;
            $this->location_data['enterprise'] = json_decode($recruit->location_data)->enterprise;
            $this->location_data['section'] = json_decode($recruit->location_data)->section;
            $this->recruit = $recruit->id;

            //   $this->group = $recruit->group;
            $this->show = true;
            $this->date_of_follow_up = date('Y-m-d');
            $this->f_name = $recruit->name_of_actor;



        } else {
            $this->reset();
        }
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
                            'conc_product_type' => 'SEED',
                            'conc_volume_sold_previous_period' => null,
                            'conc_financial_value_of_sales' => null,
                        ],

                    ]),

                'inputTwo' =>
                    collect([
                        [
                            'dom_date_recorded' => null,
                            'dom_crop_type' => 'CASSAVA',
                            'dom_market_name' => null,
                            'dom_district' => null,
                            'dom_date_of_maximum_sale' => null,
                            'dom_product_type' => 'SEED',
                            'dom_volume_sold_previous_period' => null,
                            'dom_financial_value_of_sales' => null,
                        ],
                    ]),

                'inputThree' =>
                    collect([
                        [
                            'inter_date_recorded' => null,
                            'inter_crop_type' => 'CASSAVA',
                            'inter_market_name' => null,
                            'inter_country' => null,
                            'inter_date_of_maximum_sale' => null,
                            'inter_product_type' => 'SEED',
                            'inter_volume_sold_previous_period' => null,
                            'inter_financial_value_of_sales' => null,
                        ],
                    ]),
                'aggregation_center_sales' => collect([
                    [
                        'name' => null,

                    ]
                ]),
                'market_information_systems' => collect([
                    [
                        'name' => null,

                    ]
                ]),
                'rate' => ExchangeRate::whereDate('date', date('Y-m-d'))->first()->rate ?? '',
            ]
        );

        $organisation = User::find(auth()->user()->id)->organisation;
        $this->recruits = RtcProductionProcessor::where('organisation_id', $organisation->id)->distinct()->get();

        $this->routePrefix = Route::current()->getPrefix();
        $this->total_production_value_previous_season['rate'] = $this->rate;


    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.add-follow-up');
    }
}
