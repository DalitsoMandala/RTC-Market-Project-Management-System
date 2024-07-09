<?php

namespace App\Livewire\tables\RtcMarket;

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

final class RtcProductionFarmersFollowU extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

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
        return DB::table('rpm_farmer_follow_ups');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rpm_farmer_id')
            ->add('location_data')
            ->add('date_of_follow_up_formatted', fn ($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))
            ->add('area_under_cultivation')
            ->add('number_of_plantlets_produced')
            ->add('number_of_screen_house_vines_harvested')
            ->add('number_of_screen_house_min_tubers_harvested')
            ->add('number_of_sah_plants_produced')
            ->add('area_under_basic_seed_multiplication')
            ->add('area_under_certified_seed_multiplication')
            ->add('is_registered_seed_producer')
            ->add('seed_service_unit_registration_details')
            ->add('uses_certified_seed')
            ->add('market_segment')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Rpm farmer id', 'rpm_farmer_id'),
            Column::make('Location data', 'location_data')
                ->sortable()
                ->searchable(),

            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),

            Column::make('Area under cultivation', 'area_under_cultivation')
                ->sortable()
                ->searchable(),

            Column::make('Number of plantlets produced', 'number_of_plantlets_produced')
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

            Column::make('Area under basic seed multiplication', 'area_under_basic_seed_multiplication')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication', 'area_under_certified_seed_multiplication')
                ->sortable()
                ->searchable(),

            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details', 'seed_service_unit_registration_details')
                ->sortable()
                ->searchable(),

            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),

            Column::make('Market segment', 'market_segment')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('date_of_follow_up'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

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
