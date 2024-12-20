<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RtcProductionFarmersFollowU extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public bool $deferLoading = false;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(10)
                ->pageName('farmers-follow-up')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RpmFarmerFollowUp::query()->with('farmers', 'user', 'user.organisation')
                ->whereHas('user.organisation', function ($model) use ($organisation_id) {

                    $model->where('id', $organisation_id);

                })
                ->join('rtc_production_farmers', function ($model) {
                    $model->on('rtc_production_farmers.id', '=', 'rpm_farmer_follow_ups.rpm_farmer_id');
                })->select([
                        'rpm_farmer_follow_ups.*',
                        'rtc_production_farmers.pf_id'
                    ]);

        }

        return RpmFarmerFollowUp::query()->with('farmers', 'user', 'user.organisation')->join('rtc_production_farmers', function ($model) {
            $model->on('rtc_production_farmers.id', '=', 'rpm_farmer_follow_ups.rpm_farmer_id');
        })->select([
                    'rpm_farmer_follow_ups.*',
                    'rtc_production_farmers.pf_id'
                ]);
    }

    public $namedExport = 'rpmfFU';
    #[On('export-rpmfFU')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }


    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => $model->farmers->pf_id)
            ->add('rpm_farmer_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_farmer_id;
                $row = RtcProductionFarmer::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })

            ->add('date_of_follow_up')
            ->add('date_of_follow_up_formatted', fn($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Yes' : 'No';
            })


            ->add('area_under_cultivation_total', function ($model) {

                return $model->farmers->cultivatedArea()->sum('area') ?? 0;
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



            ->add('is_registered_seed_producer', fn($model) => $model->is_registered_seed_producer == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('seed_service_unit_registration_details_date', function ($model) {

                $seed_service_unit_registration_details = json_decode($model->seed_service_unit_registration_details);

                if (is_null($seed_service_unit_registration_details)) {
                    return null;
                }

                return Carbon::parse(json_decode($model->seed_service_unit_registration_details)->registration_date)->format('d/m/Y');
            })
            ->add('seed_service_unit_registration_details_number', fn($model) => json_decode($model->seed_service_unit_registration_details)->registration_number ?? null)
            ->add('uses_certified_seed', fn($model) => $model->uses_certified_seed == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('market_segment_fresh', function ($model) {

                return $model->market_segment_fresh ? 'Fresh' : null;
            })
            ->add('market_segment_processed', function ($model) {
                return $model->market_segment_fresh ? 'Processed' : null;
            })
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')

            ->add('total_vol_production_previous_season', function ($model) {

                return $model->total_vol_production_previous_season ?? 0;
            })
            ->add('total_production_value_previous_season_total', function ($model) {

                return $model->prod_value_previous_season_total ?? 0;
            })

            ->add('total_production_value_previous_season_usd', function ($model) {

                return $model->prod_value_previous_season_usd_value ?? 0;
            })

            ->add('total_production_value_previous_season_date', function ($model) {

                return $model->prod_value_previous_season_date_of_max_sales === null ? null : Carbon::parse($model->prod_value_previous_season_date_of_max_sales)->format('d/m/Y');
            })
            ->add('usd_rate', function ($model) {

                return $model->prod_value_previous_season_usd_rate ?? 0;
            })

            ->add('total_vol_irrigation_production_previous_season', function ($model) {

                return $model->total_vol_irrigation_production_previous_season ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_total', function ($model) {
                return $model->irr_prod_value_previous_season_total ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_usd', function ($model) {
                return $model->irr_prod_value_previous_season_usd_value ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_date', function ($model) {
                return $model->irr_prod_value_previous_season_date_of_max_sales === null ? null : Carbon::parse($model->irr_prod_value_previous_season_date_of_max_sales)->format('d/m/Y');
            })
            ->add('area_basic_seed', function ($model) {

                return $model->farmers->basicSeed()->sum('area') ?? 0;
            })

            ->add('area_certified_seed', function ($model) {

                return $model->farmers->certifiedSeed()->sum('area') ?? 0;
            })
            ->add('usd_rate_irrigation', function ($model) {

                return $model->irr_prod_value_previous_season_usd_rate ?? 0;
            })
            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('sells_to_aggregation_centers', function ($model) {
                return $model->sells_to_aggregation_centers == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
        ;
    }

    public function relationSearch(): array
    {
        return [
            'farmers' => [
                'pf_id',
                'name_of_actor'
            ],
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

    public function columns(): array
    {
        return [

            Column::make('Actor ID', 'unique_id')->searchable()->sortable(),
            Column::make('Actor Name', 'actor_name'),

            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),

            Column::make('Is registered', 'is_registered')
                ->sortable()
                ->searchable(),


            Column::make('Has RTC Contractual Agreement', 'has_rtc_market_contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),



            Column::make('Number of plantlets produced/cassava', 'number_of_plantlets_produced_cassava', 'number_of_plantlets_produced->cassava')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/potato', 'number_of_plantlets_produced_potato', 'number_of_plantlets_produced->potato')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/sweet potato', 'number_of_plantlets_produced_sw_potato', 'number_of_plantlets_produced->sweet_potato')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house vines harvested', 'number_of_screen_house_vines_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house min tubers harvested', 'number_of_screen_house_min_tubers_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of sah plants produced', 'number_of_sah_plants_produced')
                ->sortable()
                ->searchable(),


            Column::make('Area under basic seed multiplication/total', 'area_basic_seed')
                ->sortable()
                ->searchable(),


            Column::make('Area under certified seed multiplication/total', 'area_certified_seed')
                ->sortable()
                ->searchable(),

            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),


            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),


            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),

            Column::make('Total production volume previous season', 'total_vol_production_previous_season', 'total_vol_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total', 'total_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total production value previous season/total ($)', 'total_production_value_previous_season_usd', 'total_production_value_previous_season->total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date', 'total_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),

            Column::make('USD Rate of Production Value', 'usd_rate', 'total_production_value_previous_season->rate')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total', 'total_irrigation_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total irrigation production value previous season/total ($)', 'total_irrigation_production_value_previous_season_usd', 'total_irrigation_production_value_previous_season->total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date', 'total_irrigation_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),


            Column::make('USD Rate of irrigation Production Value', 'usd_rate_irrigation', 'total_irrigation_production_value_previous_season->rate')
                ->sortable()
                ->searchable(),

            Column::make('Sells to domestic markets', 'sells_to_domestic_markets')
                ->sortable()
                ->searchable(),

            Column::make('Sells to international markets', 'sells_to_international_markets')
                ->sortable()
                ->searchable(),

            Column::make('Uses market information systems', 'uses_market_information_systems')
                ->sortable()
                ->searchable(),


            Column::make('Sells to aggregation centers', 'sells_to_aggregation_centers')
                ->sortable()
                ->searchable(),


            Column::make('Submitted by', 'submitted_by')

                ->searchable(),



        ];
    }

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_follow_up', ),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
