<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolRtcConsumption;
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

final class SchoolConsumptionTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return SchoolRtcConsumption::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('sc_id')
            ->add('school_name')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('date_formatted', fn($model) => Carbon::parse($model->date)->format('d/m/Y'))
            ->add('crop')
            ->add('crop_cassava')
            ->add('crop_potato')
            ->add('crop_sweet_potato')
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

    public $namedExport = 'src';
    #[On('export-src')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }




    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->sortable()->searchable(),

            Column::make('School ID', 'sc_id')->sortable()->searchable(),
            Column::make('School Name', 'school_name', 'school_name')->sortable()->searchable(),
            Column::make('District', 'district')->sortable()->searchable(),
            Column::make('EPA', 'epa', )->sortable()->searchable(),
            Column::make('Section', 'section', )->sortable()->searchable(),

            Column::make('Date', 'date_formatted', 'date')
                ->sortable()->searchable(),

            Column::make('Crop', 'crop')
                ->sortable()
                ->hidden()
                ->searchable(),

            Column::make('Cassava', 'crop_cassava')
                ->sortable()
                ->searchable(),

            Column::make('Potato', 'crop_potato')
                ->sortable()
                ->searchable(),
            Column::make('Sweet potato', 'crop_sweet_potato')
                ->sortable()
                ->searchable(),

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
