<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\User;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Exports\TableExport;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Support\Collection;

final class HouseholdRtcConsumptionTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public bool $deferLoading = true;
    public $uuid;
    public string $sortField = 'id';
    public $count = 1;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->queues(500)
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

        $user = User::find($this->userId);

        return HouseholdRtcConsumption::query();



    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('count', fn($model) => $this->count++)
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
            ->add('uuid')
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
                    return 'No';
                }

            })
            ->add('rtc_main_food_sw_potato', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'SWEET POTATO')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return 'No';
                }

            })
            ->add('rtc_main_food_cassava', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'CASSAVA')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return 'No';
                }

            })

            ->add('rtc_main_food_cassava', function ($model) {
                $data = json_decode($model->main_food_data, true);
                $data = collect($data);
                $count = $data->where('name', 'CASSAVA')->count();

                if ($count > 0) {
                    return 'Yes';
                } else {
                    return 'No';
                }

            })
            ->add('rtc_consumption_frequency')
            ->add('submitted_by', fn($model) => User::find($model->user_id)->organisation->name)
            ->add('created_at')
            ->add('created_at_formatted', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y'))
            ->add('updated_at');
    }

    public function jsonChange($data)
    {
        return json_decode($data, true);

    }

    #[On('export')]
    public function export()
    {
        $data = $this->getFilteredSortedData();
        return Excel::download(new TableExport($data), 'data.xlsx');
    }

    protected function getFilteredSortedData(): Collection
    {


        // Get the filtered and sorted data
        $data = $this->datasource()->get()->map(function ($model) {
            switch ($model) {
                case 'location_data':



                    break;

                default:
                    # code...
                    break;
            }
        });

        return collect($data);
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get()->map(function ($item) {
            return $item->toArray();
        });
    }
    public function columns(): array
    {
        return [
            Column::make('Id', 'count'),

            //  Column::make('Location id', 'location_id'),
            Column::make('Enterprise', 'enterprise', 'location_data->enterprise')
                ->sortable(),

            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa', 'location_data->epa')->sortable(),
            Column::make('Section', 'section', 'location_data->section')->sortable()->searchable(),
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

            Column::make('Age group', 'age_group', 'age_group')
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

            Column::make('Submission Date', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Submitted By', 'submitted_by')
            ,

            Column::make('UUID', 'uuid'),

        ];
    }

    public function crops()
    {
        return [
            ['name' => 'CASSAVA'],
            ['name' => 'SWEET POTATO'],
            ['name' => 'POTATO']
        ];
    }
    public function filters(): array
    {
        //  dd($this->crops());
        return [
            Filter::inputText('enterprise', 'location_data->enterprise'),
            Filter::inputText('section', 'location_data->section'),
            Filter::inputText('epa', 'location_data->epa'),
            Filter::inputText('district', 'location_data->district'),

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