<?php

namespace App\Livewire\Tables;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class HouseholdRtcConsumptionTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public function setUp(): array
    {
       // $this->showCheckBox();

        return [
            Exportable::make('export')

                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {

        return HouseholdRtcConsumption::query()->with(['location', 'mainFoods'])->where('user_id', $this->userId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('location_id')
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
            ->add('date_of_assessment_formatted', fn($model) => Carbon::parse($model->date_of_assessment)->format('d/m/Y'))
            ->add('actor_type')
            ->add('rtc_group_platform')
            ->add('producer_organisation')
            ->add('actor_name')
            ->add('age_group')
            ->add('sex')
            ->add('phone_number')
            ->add('household_size')
            ->add('under_5_in_household')
            ->add('rtc_consumers')
            ->add('rtc_consumers_potato')
            ->add('rtc_consumers_sw_potato')
            ->add('rtc_consumers_cassava')
            ->add('rtc_main_food_potato', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'POTATO')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return '';
                }

            })
            ->add('rtc_main_food_sw_potato', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'SWEET POTATO')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return '';
                }

            })
            ->add('rtc_main_food_cassava', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'CASSAVA')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return '';
                }

            })
            ->add('rtc_consumption_frequency')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            //  Column::make('Location id', 'location_id'),
            Column::make('Enterprise', 'enterprise', 'location_data->enterprise'),
            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa'),
            Column::make('Section', 'section'),
            Column::make('Date of assessment', 'date_of_assessment_formatted', 'date_of_assessment')
                ->sortable(),

            Column::make('Actor type', 'actor_type')
                ->sortable()
                ->searchable(),

            Column::make('Rtc group platform', 'rtc_group_platform')
                ->sortable()
                ->searchable(),

            Column::make('Producer organisation', 'producer_organisation')
                ->sortable()
                ->searchable(),

            Column::make('Actor name', 'actor_name')
                ->sortable()
                ->searchable(),

            Column::make('Age group', 'age_group')
                ->sortable()
                ->searchable(),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Household size', 'household_size')
                ->sortable()
                ->searchable(),

            Column::make('Under 5 in household', 'under_5_in_household')
                ->sortable()
                ->searchable(),

            Column::make('Rtc consumers', 'rtc_consumers')
                ->sortable()
                ->searchable(),
            Column::make('Rtc consumers/Potato', 'rtc_consumers_potato')
                ->sortable()
                ->searchable(),
            Column::make('Rtc consumers/Sweet Potato', 'rtc_consumers_sw_potato')
                ->sortable()
                ->searchable(),
            Column::make('Rtc consumers/Cassava', 'rtc_consumers_cassava')
                ->sortable()
                ->searchable(),

            Column::make('Rtc consumption frequency', 'rtc_consumption_frequency')
                ->sortable()
                ->searchable(),
            Column::make('RTC MAIN FOOD/CASSAVA', 'rtc_main_food_cassava')
            ,
            Column::make('RTC MAIN FOOD/POTATO', 'rtc_main_food_potato')
            ,
            Column::make('RTC MAIN FOOD/SWEET POTATO', 'rtc_main_food_sw_potato')
            ,

            Column::make('Submission Date', 'created_at')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('date_of_assessment'),
        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
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
