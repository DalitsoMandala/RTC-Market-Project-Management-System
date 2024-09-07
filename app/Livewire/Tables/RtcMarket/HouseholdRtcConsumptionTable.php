<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Exports\rtcmarket\HouseholdExport\ExportData;
use App\Models\User;
use App\Models\Submission;
use App\Jobs\ExportDataJob;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Exports\TableExport;
use App\Jobs\ExcelExportJob;
use Illuminate\Support\Carbon;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Lazy;
use PowerComponents\LivewirePowerGrid\Cache;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Ramsey\Uuid\Uuid;

final class HouseholdRtcConsumptionTable extends PowerGridComponent
{

    use WithExport;

    public $userId;
    public bool $deferLoading = false;
    public $uuid;
    public string $sortField = 'id';
    public $count = 1;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Lazy::make()
                ->rowsPerChildren(25),

            Header::make()->includeViewOnTop('components.export-data')

                ->showSearchInput()






            ,
            Footer::make()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {



        return HouseholdRtcConsumption::query()->with(['mainFoods']);



    }



    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('count', fn($model) => $this->count++)
            ->add('location_id')
            ->add('enterprise', function ($model) {
                return $model->enterprise ?? null;
            })
            ->add('district', function ($model) {
                return $model->district ?? null;
            })
            ->add('epa', function ($model) {
                return $model->epa ?? null;
            })
            ->add('section', function ($model) {
                return $model->section ?? null;
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



                return $model->mainFoods->pluck('name')->contains('Potato') ? 'Potato' : '';

            })
            ->add('rtc_main_food_sw_potato', function ($model) {
                return $model->mainFoods->pluck('name')->contains('Sweet potato') ? 'Sweet potato' : '';

            })


            ->add('rtc_main_food_cassava', function ($model) {
                return $model->mainFoods->pluck('name')->contains('Cassava') ? 'Cassava' : '';
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


    public $batchID;
    public $exporting = false;
    public $exportFinished = false;

    public $exportFailed = false;

    public $exportUniqueId = '';
    #[On('export')]
    public function export()
    {
        $this->exporting = true;
        $this->exportFinished = false;
        $this->exportFailed = false;
        $this->exportUniqueId = Uuid::uuid4()->toString();
        $batch = Bus::batch([
            new ExcelExportJob('hrc', $this->exportUniqueId),
        ])->dispatch();

        $this->batchID = $batch->id;

    }


    public function getExportBatchProperty()
    {
        if (!$this->batchID) {
            return null;
        }

        return Bus::findBatch($this->batchID);
    }

    public function downloadExport()
    {
        return Storage::download('public/exports/household-rtc-consumption_' . $this->exportUniqueId . '.xlsx');
    }

    public function updateExportProgress()
    {
        $this->exportFinished = $this->exportBatch->finished();

        if ($this->exportFinished && $this->exportBatch->failedJobs === 0) {
            $this->exporting = false;
            $this->exportFailed = false;
        } else if ($this->exportFinished && $this->exportBatch->failedJobs > 0) {
            $this->exporting = false;
            $this->exportFailed = true;
        }

    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }


    public function relationSearch(): array
    {
        return [

            'mainFoods' => [
                'name'
            ]
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),

            //  Column::make('Location id', 'location_id'),
            Column::make('Enterprise', 'enterprise')
                ->searchable()
                ->sortable(),

            Column::make('District', 'district')->searchable(),
            Column::make('EPA', 'epa')->sortable()->searchable(),
            Column::make('Section', 'section')->sortable()->searchable()
            // ->searchable()
            ,
            Column::make('Date of assessment', 'date_of_assessment_formatted', 'date_of_assessment')
                ->sortable(),

            Column::make('Actor type', 'actor_type', 'actor_type')
                ->sortable()
                ->searchable()
            ,

            Column::make('Rtc group platform', 'rtc_group_platform', 'rtc_group_platform')
                ->sortable()
                ->searchable()
            ,

            Column::make('Producer organisation', 'producer_organisation', 'producer_organisation')
                ->sortable()
                ->searchable()
            ,

            Column::make('Actor name', 'actor_name', 'actor_name')
                ->sortable()
                ->searchable()
            ,

            Column::make('Age group', 'age_group', 'age_group')
                ->sortable()
                ->searchable()
            ,

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable()
            ,

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable()
            ,

            Column::make('Household size', 'household_size')
                ->sortable()
                ->searchable()
            ,

            Column::make('Under 5 in household', 'under_5_in_household')
                ->sortable()
                ->searchable()
            ,

            Column::make('Rtc consumers', 'rtc_consumers')
                ->sortable()
                ->searchable()
            ,
            Column::make('Rtc consumers/Potato', 'rtc_consumers_potato', 'rtc_consumers')
                ->sortable()
                ->searchable()
            ,
            Column::make('Rtc consumers/Sweet Potato', 'rtc_consumers_sw_potato', 'rtc_consumers')
                ->sortable()
                ->searchable()
            ,
            Column::make('Rtc consumers/Cassava', 'rtc_consumers_cassava', 'rtc_consumers')
                ->sortable()
                ->searchable()
            ,

            Column::make('Rtc consumption frequency', 'rtc_consumption_frequency')
                ->sortable()
                ->searchable()
            ,
            Column::make('RTC MAIN FOOD/CASSAVA', 'rtc_main_food_cassava')
            ,
            Column::make('RTC MAIN FOOD/POTATO', 'rtc_main_food_potato')
            ,
            Column::make('RTC MAIN FOOD/SWEET POTATO', 'rtc_main_food_sw_potato')
            ,

            Column::make('Submission Date', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable()
            ,

            Column::make('Submitted By', 'submitted_by')->searchable()
            ,

            Column::make('UUID', 'uuid')->searchable(),

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
            // Filter::inputText('enterprise', 'location_data->enterprise'),
            // Filter::inputText('section', 'location_data->section'),
            // Filter::inputText('epa', 'location_data->epa'),
            // Filter::inputText('district', 'location_data->district'),
            // Filter::inputText('rtc_main_food_potato')
            //     ->operators(['contains'])
            //     ->builder(function (Builder $builder, mixed $value) {
            //         $getValue = strtolower($value['value']);
            //         if ($getValue == 'Yes') {
            //             $builder->whereJsonContains('main_food_data', 'POTATO');
            //         } else {
            //             $builder->whereJsonDoesntContain('main_food_data', 'POTATO');
            //         }

            //     }),

            // Filter::inputText('rtc_main_food_cassava')
            //     ->operators(['contains'])
            //     ->builder(function (Builder $builder, mixed $value) {
            //         $getValue = strtolower($value['value']);
            //         if ($getValue == 'Yes') {
            //             $builder->whereJsonContains('main_food_data', 'CASSAVA');
            //         } else {
            //             $builder->whereJsonDoesntContain('main_food_data', 'CASSAVA');
            //         }

            //     }),

            // Filter::inputText('rtc_main_food_sw_potato')
            //     ->operators(['contains'])
            //     ->builder(function (Builder $builder, mixed $value) {
            //         $getValue = strtolower($value['value']);
            //         if ($getValue == 'Yes') {
            //             $builder->whereJsonContains('main_food_data', 'SWEET POTATO');
            //         } else {
            //             $builder->whereJsonDoesntContain('main_food_data', 'SWEET POTATO');
            //         }

            //     }),
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
