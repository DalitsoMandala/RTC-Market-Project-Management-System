<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Traits\UITrait;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final class RtcProductionFarmersTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    use UITrait;
    public $routePrefix;
    public bool $deferLoading = false;
    public $nameOfTable = 'RTC Production Farmers Table';
    public $descriptionOfTable = 'Data of RTC production farmers';
    public function setUp(): array
    {
        //  $this->showCheckBox();
        // $columns = $this->columns();
        // $getMap = [];
        // foreach ($columns as $column) {
        //     $getMap[$column->title] = $column->dataField;
        // }
        // dd($getMap);
        return [

            Header::make()->includeViewOnTop('components.export-data')
                ->showSearchInput()
            //     ->includeViewOnTop('components.export-data-farmers')
            ,
            Footer::make()
                ->showPerPage(10)
                ->pageName('farmers')
                ->showRecordCount(),
        ];
    }

    public function datasource(): EloquentBuilder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = RtcProductionFarmer::query()->with([
            'user',
            'user.organisation'
        ])->select([
            'rtc_production_farmers.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {

            return $query->where('organisation_id', $organisation_id);
        }

        return $query;
    }


    public $namedExport = 'rpmf';
    #[On('export-rpmf')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }


    #[On('download-export')]
    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rn')
            ->add('unique_id', fn($model) => $model->pf_id)
            ->add('date_of_followup_formatted', fn($model) => Carbon::parse($model->date_of_followup)->format('d/m/Y'))

            ->add('group_name')


            ->add('enterprise', function ($model) {

                return $model->enterprise;
            })
            ->add('district', function ($model) {

                return $model->district;
            })
            ->add('epa', function ($model) {

                return $model->epa;
            })
            ->add('section', function ($model) {

                return $model->section;
            })

            ->add('uses_certified_seed', fn($model) => $this->booleanUI($model->uses_certified_seed, $model->uses_certified_seed == 1, true))

            ->add('area_under_cultivation_total', function ($model) {

                return $model->cultivatedArea()->sum('area') ?? 0;
            })

            ->add('number_of_plantlets_produced_potato', function ($model) {

                return $model->number_of_plantlets_produced_potato ?? 0;
            })
            ->add('number_of_plantlets_produced_cassava', function ($model) {

                return $model->number_of_plantlets_produced_cassava;
            })
            ->add('number_of_plantlets_produced_sw_potato', function ($model) {

                return $model->number_of_plantlets_produced_sweet_potato;
            })
            ->add('number_of_screen_house_vines_harvested', fn($model) => $model->number_of_screen_house_vines_harvested ?? 0)
            ->add('number_of_screen_house_min_tubers_harvested', fn($model) => $model->number_of_screen_house_min_tubers_harvested ?? 0)
            ->add('number_of_sah_plants_produced', fn($model) => $model->number_of_sah_plants_produced ?? 0)


            ->add('is_registered_seed_producer', fn($model) => $this->booleanUI($model->is_registered_seed_producer, $model->is_registered_seed_producer == 1, true))
            ->add('seed_service_unit_registration_details_date', function ($model) {


                if (is_null($model->registration_date_seed_producer)) {
                    return null;
                }

                return Carbon::parse($model->registration_date_seed_producer)->format('d/m/Y');
            })
            ->add('seed_service_unit_registration_details_number', fn($model) => $model->registration_number_seed_producer ?? null)
            ->add('uses_certified_seed', fn($model) => $this->booleanUI($model->uses_certified_seed, $model->uses_certified_seed == 1, true))
            ->add('market_segment_fresh', function ($model) {

                return $this->booleanUI($model->market_segment_fresh, $model->market_segment_fresh == 1, true);
            })
            ->add('market_segment_processed', function ($model) {
                return $this->booleanUI($model->market_segment_processed, $model->market_segment_processed == 1, true);
            })

            ->add('market_segment_cuttings', function ($model) {
                return $this->booleanUI($model->market_segment_cuttings, $model->market_segment_cuttings == 1, true);
            })
            ->add('has_rtc_market_contract', fn($model) => $this->booleanUI($model->has_rtc_market_contract, $model->has_rtc_market_contract == 1, true))
            ->add('date_of_followup', fn($model) => $model->date_of_followup ? Carbon::parse($model->date_of_followup)->format('d/m/Y') : null)
            ->add('total_vol_production_previous_season', fn($model) => $model->total_vol_production_previous_season ?? null)
            ->add('total_vol_production_previous_season_produce', fn($model) => $model->total_vol_production_previous_season_produce ?? null)
            ->add('total_vol_production_previous_season_seed', fn($model) => $model->total_vol_production_previous_season_seed ?? null)
            ->add('total_vol_production_previous_season_cuttings', fn($model) => $model->total_vol_production_previous_season_cuttings ?? null)
            ->add('total_vol_production_previous_season_seed_bundle', fn($model) => $model->total_vol_production_previous_season_seed_bundle ?? null)
            ->add('prod_value_previous_season_total', fn($model) => $model->prod_value_previous_season_total ?? null)
            ->add('prod_value_previous_season_produce', fn($model) => $model->prod_value_previous_season_produce ?? null)
            ->add('prod_value_previous_season_seed', fn($model) => $model->prod_value_previous_season_seed ?? null)
            ->add('prod_value_previous_season_seed_bundle', fn($model) => $model->prod_value_previous_season_seed_bundle ?? null)
            ->add('prod_value_previous_season_cuttings', fn($model) => $model->prod_value_previous_season_cuttings ?? null)
            ->add('prod_value_produce_prevailing_price', fn($model) => $model->prod_value_produce_prevailing_price ?? null)
            ->add('prod_value_seed_prevailing_price', fn($model) => $model->prod_value_seed_prevailing_price ?? null)
            ->add('prod_value_cuttings_prevailing_price', fn($model) => $model->prod_value_cuttings_prevailing_price ?? null)
            ->add('prod_value_previous_season_usd_rate', fn($model) => $model->prod_value_previous_season_usd_rate ?? null)
            ->add('prod_value_previous_season_usd_value', fn($model) => $model->prod_value_previous_season_usd_value ?? null)
            ->add('total_vol_irrigation_production_previous_season', fn($model) => $model->total_vol_irrigation_production_previous_season ?? null)
            ->add('total_vol_irrigation_production_previous_season_produce', fn($model) => $model->total_vol_irrigation_production_previous_season_produce ?? null)
            ->add('total_vol_irrigation_production_previous_season_seed', fn($model) => $model->total_vol_irrigation_production_previous_season_seed ?? null)
            ->add('total_vol_irrigation_production_previous_season_cuttings', fn($model) => $model->total_vol_irrigation_production_previous_season_cuttings ?? null)
            ->add('total_vol_irrigation_production_previous_season_seed_bundle', fn($model) => $model->total_vol_irrigation_production_previous_season_seed_bundle ?? null)
            ->add('irr_prod_value_previous_season_total', fn($model) => $model->irr_prod_value_previous_season_total ?? null)
            ->add('irr_prod_value_previous_season_produce', fn($model) => $model->irr_prod_value_previous_season_produce ?? null)
            ->add('irr_prod_value_previous_season_seed', fn($model) => $model->irr_prod_value_previous_season_seed ?? null)
            ->add('irr_prod_value_previous_season_seed_bundle', fn($model) => $model->irr_prod_value_previous_season_seed_bundle ?? null)
            ->add('irr_prod_value_previous_season_cuttings', fn($model) => $model->irr_prod_value_previous_season_cuttings ?? null)
            ->add('irr_prod_value_produce_prevailing_price', fn($model) => $model->irr_prod_value_produce_prevailing_price ?? null)
            ->add('irr_prod_value_seed_prevailing_price', fn($model) => $model->irr_prod_value_seed_prevailing_price ?? null)
            ->add('irr_prod_value_cuttings_prevailing_price', fn($model) => $model->irr_prod_value_cuttings_prevailing_price ?? null)
            ->add('irr_prod_value_previous_season_usd_rate', fn($model) => $model->irr_prod_value_previous_season_usd_rate ?? null)
            ->add('irr_prod_value_previous_season_usd_value', fn($model) => $model->irr_prod_value_previous_season_usd_value ?? null)

            ->add('sells_to_domestic_markets', fn($model) => $this->booleanUI(
                $model->sells_to_domestic_markets,
                $model->sells_to_domestic_markets == 1,
                true
            ))
            ->add('sells_to_international_markets', fn($model) => $this->booleanUI(
                $model->sells_to_international_markets,
                $model->sells_to_international_markets == 1,
                true
            ))
            ->add('uses_market_information_systems', fn($model) => $this->booleanUI(
                $model->uses_market_information_systems,
                $model->uses_market_information_systems == 1,
                true
            ))

            ->add('total_vol_aggregation_center_sales')

            ->add('sells_to_aggregation_centers', function ($model) {
                return $this->booleanUI(
                    $model->sells_to_aggregation_centers,
                    $model->sells_to_aggregation_centers == 1,
                    true
                );
            })


            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }
            })
        ;
    }



    public function columns(): array
    {
        return [
            Column::make('ID', 'rn')->sortable(),
            Column::make('Farmer ID', 'unique_id', 'pf_id')->sortable()->searchable(),
            Column::make('Group Name', 'group_name'),
            Column::make('Date of follow up', 'date_of_followup')
                ->sortable(),


            Column::make('Enterprise', 'enterprise')->sortable()->searchable(),
            Column::make('District', 'district')->sortable()->searchable(),
            Column::make('EPA', 'epa')->sortable()->searchable(),
            Column::make('Section', 'section')->sortable()->searchable(),









            Column::make('Number of plantlets produced/cassava', 'number_of_plantlets_produced_cassava', 'number_of_plantlets_produced->cassava')
                ->sortable(),
            Column::make('Number of plantlets produced/potato', 'number_of_plantlets_produced_potato', 'number_of_plantlets_produced->potato')
                ->sortable(),
            Column::make('Number of plantlets produced/sweet potato', 'number_of_plantlets_produced_sw_potato', 'number_of_plantlets_produced->sweet_potato')
                ->sortable(),

            Column::make('Number of screen house vines harvested', 'number_of_screen_house_vines_harvested')
                ->sortable(),

            Column::make('Number of screen house min tubers harvested', 'number_of_screen_house_min_tubers_harvested')
                ->sortable(),

            Column::make('Number of sah plants produced', 'number_of_sah_plants_produced')
                ->sortable(),


            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable(),


            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable(),


            Column::make('Market segment (Fresh)', 'market_segment_fresh')
                ->sortable(),


            Column::make('Market segment (Processed)', 'market_segment_processed')
                ->sortable(),


            Column::make('Market segment (Cuttings)', 'market_segment_cuttings')
                ->sortable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable(),

            Column::make('Volume of production (Produce)', 'total_vol_production_previous_season_produce'),
            Column::make('Volume of production (Seed)', 'total_vol_production_previous_season_seed'),
            Column::make('Volume of production (Cuttings)', 'total_vol_production_previous_season_cuttings'),
            Column::make('Volume of production Seed Bundle', 'total_vol_production_previous_season_seed_bundle'),
            Column::make('Total volume of production (Metric Tonnes)', 'total_vol_production_previous_season'),
            Column::make('Value of Production (Produce)', 'prod_value_previous_season_produce'),
            Column::make('Value of Production (Produce Prevailing Price)', 'prod_value_produce_prevailing_price'),
            Column::make('Value of Production (Seed)', 'prod_value_previous_season_seed'),
            Column::make('Value of Production (Seed Prevailing Price)', 'prod_value_seed_prevailing_price'),
            Column::make('Value of Production (Cuttings)', 'prod_value_previous_season_cuttings'),
            Column::make('Value of Production (Cuttings Prevailing Price)', 'prod_value_cuttings_prevailing_price'),
            Column::make('Value of Production Seed Bundle', 'prod_value_previous_season_seed_bundle'),
            Column::make('Total Value of Production (Metric Tonnes)', 'prod_value_previous_season_total'),
            Column::make('Total Value of Production (USD Rate)', 'prod_value_previous_season_usd_rate'),
            Column::make('Total Value of Production (USD)', 'prod_value_previous_season_usd_value'),



            Column::make('Volume of irrigation production (Produce)', 'total_vol_irrigation_production_previous_season_produce'),
            Column::make('Volume of irrigation production (Seed)', 'total_vol_irrigation_production_previous_season_seed'),
            Column::make('Volume of irrigation production (Cuttings)', 'total_vol_irrigation_production_previous_season_cuttings'),
            Column::make('Volume of irrigation production Seed Bundle', 'total_vol_irrigation_production_previous_season_seed_bundle'),
            Column::make('Total volume of irrigation production (Metric Tonnes)', 'total_vol_irrigation_production_previous_season'),
            Column::make('Value of irrigation Production (Produce)', 'irr_prod_value_previous_season_produce'),
            Column::make('Value of irrigation Production (Produce Prevailing Price)', 'irr_prod_value_produce_prevailing_price'),
            Column::make('Value of irrigation Production (Seed)', 'irr_prod_value_previous_season_seed'),
            Column::make('Value of irrigation Production (Seed Prevailing Price)', 'irr_prod_value_seed_prevailing_price'),
            Column::make('Value of irrigation Production (Cuttings)', 'irr_prod_value_previous_season_cuttings'),
            Column::make('Value of irrigation Production (Cuttings Prevailing Price)', 'irr_prod_value_cuttings_prevailing_price'),
            Column::make('Value of irrigation Production Seed Bundle', 'irr_prod_value_previous_season_seed_bundle'),
            Column::make('Total Value of irrigation Production (Metric Tonnes)', 'irr_prod_value_previous_season_total'),
            Column::make('Total Value of irrigation Production (USD Rate)', 'irr_prod_value_previous_season_usd_rate'),
            Column::make('Total Value of irrigation Production (USD)', 'irr_prod_value_previous_season_usd_value'),

            Column::make('Sells to domestic markets', 'sells_to_domestic_markets')
                ->sortable(),

            Column::make('Sells to international markets', 'sells_to_international_markets')
                ->sortable(),

            Column::make('Uses market information systems', 'uses_market_information_systems')
                ->sortable(),


            Column::make('Sells to aggregation centers', 'sells_to_aggregation_centers')
                ->sortable(),

            Column::make('Total Volume of Aggregation Center Sales', 'total_vol_aggregation_center_sales')
                ->sortable(),


            Column::make('Submitted by', 'submitted_by')

                ->searchable(),




        ];
    }

    public function relationSearch(): array
    {
        return [

            'user' => [
                'name',

            ],

            'user.organisation' => [
                'name'
            ]

        ];
    }


    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }



    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function openModal($id)
    {

        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        return redirect()->to('' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $id . '');
    }




    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            // Rule::button('add-follow-up')
            //     ->when(fn($row) => !(RpmFarmerFollowUp::find($row->id)))
            //     ->hide(),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'your-custom-table-id',  // Set your custom HTML ID here
            // You can add other HTML attributes as needed
        ];
    }
}
