<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\RtcProductionProcessor;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class RtcProductionProcessorsFollowU extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //  $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('rpm_processor_follow_ups');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rpm_processor_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_processor_id;
                $row = RtcProductionProcessor::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })
            ->add('group_name', function ($model) {
                $data = json_decode($model->location_data);
                return $data->group_name;
            })
            ->add('date_of_follow_up_formatted', fn($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))
            ->add('market_segment_fresh', fn($model) => json_decode($model->market_segment)->fresh ?? null)
            ->add('market_segment_processed', fn($model) => json_decode($model->market_segment)->processed ?? null)
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')
            ->add('total_vol_production_previous_season', fn($model) => $model->total_vol_production_previous_season ?? 0)
            ->add('total_production_value_previous_season_total', fn($model) => json_decode($model->total_production_value_previous_season)->total ?? 0)
            ->add('total_production_value_previous_season_date', fn($model) => Carbon::parse(json_decode($model->total_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y') ?? null)
            ->add('total_vol_irrigation_production_previous_season', fn($model) => $model->total_vol_irrigation_production_previous_season ?? 0)
            ->add('total_irrigation_production_value_previous_season_total', fn($model) => json_decode($model->total_irrigation_production_value_previous_season)->total ?? 0)
            ->add('total_irrigation_production_value_previous_season_date', fn($model) => Carbon::parse(json_decode($model->total_irrigation_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y') ?? null)
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? 'Yes' : 'No')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? 'Yes' : 'No')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? 'Yes' : 'No')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('aggregation_centers_response', fn($model) => json_decode($model->aggregation_centers)->response == 1 ? 'Yes' : 'No' ?? null)
            ->add('aggregation_centers_specify', fn($model) => json_decode($model->aggregation_centers)->specify ?? null)
            ->add('aggregation_center_sales')
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Actor Name', 'actor_name'),
            Column::make('Actor id', 'rpm_processor_id'),
            Column::make('Group name', 'group_name'),


            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),

            Column::make('Market segment/fresh', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment/processed', 'market_segment_processed')
                ->sortable()
                ->searchable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),

            Column::make('Total production previous season', 'total_vol_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production previous season', 'total_vol_irrigation_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date')
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

            Column::make('Market information systems', 'market_information_systems')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation centers/Response', 'aggregation_centers_response')
                ->sortable()
                ->searchable(),


            Column::make('Aggregation centers/Specify', 'aggregation_centers_specify')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation center sales', 'aggregation_center_sales')
                ->sortable()
                ->searchable(),



        ];
    }

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_follow_up'),
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