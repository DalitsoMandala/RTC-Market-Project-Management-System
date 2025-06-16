<?php

namespace App\Livewire\tables\rtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use App\Models\RtcConsumption;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolRtcConsumption;
use App\Traits\UITrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
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

final class RtcConsumptionTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    use UITrait;
    public $nameOfTable = "RTC Consumption Table";
    public $descriptionOfTable = "Data of RTC Consumption";
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage(10)
                ->pageName('consumption')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {


        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = RtcConsumption::query()->select([
            'rtc_consumptions.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {
            return $query->where('organisation_id', $organisation_id);
        }
        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('sc_id')
            ->add('entity_name')
            ->add('entity_type')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('date_formatted', fn($model) => $model->date ? Carbon::parse($model->date)->format('d/m/Y') : 'NA')
            ->add('crop')
            ->add('crop_cassava', fn($model) => $this->booleanUI($model->crop_cassava, $model->crop_cassava == 1))
            ->add('crop_potato', fn($model) => $this->booleanUI($model->crop_potato, $model->crop_potato == 1))
            ->add('crop_sweet_potato', fn($model) => $this->booleanUI($model->crop_sweet_potato, $model->crop_sweet_potato == 1))
            ->add('male_count')
            ->add('female_count')
            ->add('total', function ($model) {

                return ($model->male_count + $model->female_count) ?? 0;
            })
            ->add('uuid')
            ->add('user_id')
            ->add('created_at')
            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }
            })
            ->add('updated_at');
    }

    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }

    public $namedExport = 'rtcConsumption';
    #[On('export-rtcConsumption')]
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




    public function columns(): array
    {
        return [
            Column::make('Id', 'rn')->sortable()->searchable(),
            Column::make('Entity ID', 'en_id')->searchable(),
            Column::make('Entity Name', 'entity_name', 'entity_name')->searchable(),
            Column::make('Entity Type', 'entity_type', 'entity_type')->sortable()->searchable(),
            Column::make('District', 'district')->sortable()->searchable(),
            Column::make('EPA', 'epa',)->sortable()->searchable(),
            Column::make('Section', 'section',)->sortable()->searchable(),

            Column::make('Date', 'date_formatted', 'date')
                ->sortable()->searchable(),


            Column::make('Cassava', 'crop_cassava')
                ->sortable(),

            Column::make('Potato', 'crop_potato')
                ->sortable(),
            Column::make('Sweet potato', 'crop_sweet_potato')
                ->sortable(),

            Column::make('Male count', 'male_count')
                ->sortable()
                ->searchable(),

            Column::make('Female count', 'female_count')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total', 'total')
                ->sortable()
                ->searchable(),
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

    public function filters(): array
    {
        return [
            //    Filter::datepicker('date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }
}
