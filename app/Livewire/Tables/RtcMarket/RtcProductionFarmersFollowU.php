<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;
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
    public bool $deferLoading = true;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return RpmFarmerFollowUp::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rpm_farmer_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_farmer_id;
                $row = RtcProductionFarmer::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })
            ->add('location_data')
            ->add('enterprise', function ($model) {
                $data = json_decode($model->location_data);
                return $data->enterprise;
            })
            ->add('district', function ($model) {
                $data = json_decode($model->location_data);
                return $data->district;
            })
            ->add('epa', function ($model) {
                $data = json_decode($model->location_data);

                return $data->epa;
            })
            ->add('section', function ($model) {
                $data = json_decode($model->location_data);
                return $data->section;
            })

            ->add('group_name', function ($model) {
                $data = json_decode($model->location_data);
                return $data->group_name;
            })

            ->add('date_of_follow_up')
            ->add('date_of_follow_up_formatted', fn($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))
            ->add('area_under_cultivation', function ($model) {
                return json_decode($model->area_under_cultivation)->total ?? 0;
            })

            //   ->add('area_under_cultivation_total', fn($model) => json_decode($model->area_under_cultivation)->total)
            ->add('area_under_cultivation_variety_1', fn($model) => json_decode($model->area_under_cultivation)->variety_1 ?? null)
            ->add('area_under_cultivation_variety_2', fn($model) => json_decode($model->area_under_cultivation)->variety_2 ?? null)
            ->add('area_under_cultivation_variety_3', fn($model) => json_decode($model->area_under_cultivation)->variety_3 ?? null)
            ->add('area_under_cultivation_variety_4', fn($model) => json_decode($model->area_under_cultivation)->variety_4 ?? null)
            ->add('area_under_cultivation_variety_5', fn($model) => json_decode($model->area_under_cultivation)->variety_5 ?? null)

            ->add('number_of_plantlets_produced')
            ->add('number_of_plantlets_produced_potato', fn($model) => json_decode($model->number_of_plantlets_produced)->potato ?? null ?? null)
            ->add('number_of_plantlets_produced_cassava', fn($model) => json_decode($model->number_of_plantlets_produced)->cassava ?? null ?? null)
            ->add('number_of_plantlets_produced_sw_potato', fn($model) => json_decode($model->number_of_plantlets_produced)->sweet_potato ?? null ?? null)

            ->add('number_of_screen_house_vines_harvested', fn($model) => $model->number_of_screen_house_vines_harvested ?? null)
            ->add('number_of_screen_house_min_tubers_harvested', fn($model) => $model->number_of_screen_house_min_tubers_harvested ?? null)
            ->add('number_of_sah_plants_produced', fn($model) => $model->number_of_sah_plants_produced ?? null)
            ->add('area_under_basic_seed_multiplication')
            ->add('basic_seed_multiplication_total', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->total ?? null)
            ->add('basic_seed_multiplication_variety_1', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_1 ?? null)
            ->add('basic_seed_multiplication_variety_2', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_2 ?? null)
            ->add('basic_seed_multiplication_variety_3', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_3 ?? null)
            ->add('basic_seed_multiplication_variety_4', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_4 ?? null)
            ->add('basic_seed_multiplication_variety_5', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_5 ?? null)
            ->add('basic_seed_multiplication_variety_6', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_6 ?? null)
            ->add('basic_seed_multiplication_variety_7', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_7 ?? null)
            ->add('area_under_certified_seed_multiplication')
            ->add('area_under_certified_seed_multiplication_total', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->total ?? null)
            ->add('area_under_certified_seed_multiplication_variety_1', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_1 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_2', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_2 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_3', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_3 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_4', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_4 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_5', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_5 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_6', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_6 ?? null)
            ->add('area_under_certified_seed_multiplication_variety_7', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_7 ?? null)
            ->add('is_registered_seed_producer', fn($model) => $model->is_registered_seed_producer == 1 ? 'Yes' : 'No')
            ->add('seed_service_unit_registration_details_date', function ($model) {
                $details = json_decode($model->seed_service_unit_registration_details);
                return $details && isset($details->registration_date)
                    ? Carbon::parse($details->registration_date)->format('d/m/Y')
                    : null;
            })
            ->add('seed_service_unit_registration_details_number', fn($model) => json_decode($model->seed_service_unit_registration_details)->registration_number ?? null)
            ->add('service_unit_date', fn($model) => Carbon::parse($model->service_unit_date)->format('d/m/Y'))
            ->add('service_unit_number')
            ->add('uses_certified_seed', fn($model) => $model->uses_certified_seed == 1 ? 'Yes' : 'No')
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Actor Name', 'actor_name'),
            Column::make('Actor id', 'rpm_farmer_id'),
            Column::make('Group name', 'group_name'),
            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),
            Column::make('Area under cultivation/total', 'area_under_cultivation')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 1', 'area_under_cultivation_variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 2', 'area_under_cultivation_variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 3', 'area_under_cultivation_variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 4', 'area_under_cultivation_variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 5', 'area_under_cultivation_variety_5')
                ->sortable()
                ->searchable(),


            Column::make('Number of plantlets produced/cassava', 'number_of_plantlets_produced_cassava')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/potato', 'number_of_plantlets_produced_potato')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/sweet potato', 'number_of_plantlets_produced_sw_potato')
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

            Column::make('Area under basic seed multiplication/total', 'basic_seed_multiplication_total')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety', 'basic_seed_multiplication_variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 2', 'basic_seed_multiplication_variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 3', 'basic_seed_multiplication_variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 4', 'basic_seed_multiplication_variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 5', 'basic_seed_multiplication_variety_5')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 6', 'basic_seed_multiplication_variety_6')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 7', 'basic_seed_multiplication_variety_7')
                ->sortable()
                ->searchable(),



            Column::make('Area under certified seed multiplication/total', 'area_under_certified_seed_multiplication_total')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety', 'area_under_certified_seed_multiplication_variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 2', 'area_under_certified_seed_multiplication_variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 3', 'area_under_certified_seed_multiplication_variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 4', 'area_under_certified_seed_multiplication_variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 5', 'area_under_certified_seed_multiplication_variety_5')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 6', 'area_under_certified_seed_multiplication_variety_6')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 7', 'area_under_certified_seed_multiplication_variety_7')
                ->sortable()
                ->searchable(),



            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details/reg. date', 'seed_service_unit_registration_details_date')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details/ reg. number', 'seed_service_unit_registration_details_number')
                ->sortable()
                ->searchable(),

            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
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
