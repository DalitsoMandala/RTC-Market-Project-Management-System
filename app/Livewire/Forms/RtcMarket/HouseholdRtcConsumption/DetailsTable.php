<?php

namespace App\Livewire\forms\rtcMarket\HouseholdRtcConsumption;

use App\Models\HrcLocation;
use App\Models\Submission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class DetailsTable extends PowerGridComponent
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

    public function datasource(): Collection
    {
        $route = Route::current();

        $submissions = Submission::where('batch_no', $route->id)->first();
        $batch_data = $submissions->data;

        $decoded_data = json_decode($batch_data, true);
        $count = 1;
        $anotherArray = [];
        foreach ($decoded_data as $data) {

            $data['id'] = $count++;
            $anotherArray[] = $data;
        }

        return collect($anotherArray);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('location_id')
            ->add('enterprise', function ($model) {
                $location = HrcLocation::find($model->location_id);
                if ($location) {
                    return $location->enterprise;

                }

            })
            ->add('district', function ($model) {
                $location = HrcLocation::find($model->location_id);
                if ($location) {
                    return $location->district;

                }

                return $model->location->district;
            })
            ->add('epa', function ($model) {
                $location = HrcLocation::find($model->location_id);
                if ($location) {
                    return $location->epa;

                }

            })
            ->add('section', function ($model) {
                $location = HrcLocation::find($model->location_id);
                if ($location) {
                    return $location->section;

                }

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
            ->add('rtc_consumption_frequency')
            ->add('uuid')
            ->add('file_link')
            ->add('user_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Enterprise', 'enterprise'),
            Column::make('District', 'district'),
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

            Column::make('Rtc consumers potato', 'rtc_consumers_potato')
                ->sortable()
                ->searchable(),

            Column::make('Rtc consumers sw potato', 'rtc_consumers_sw_potato')
                ->sortable()
                ->searchable(),

            Column::make('Rtc consumers cassava', 'rtc_consumers_cassava')
                ->sortable()
                ->searchable(),

            Column::make('Rtc consumption frequency', 'rtc_consumption_frequency')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
           // Filter::datepicker('date_of_assessment'),
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