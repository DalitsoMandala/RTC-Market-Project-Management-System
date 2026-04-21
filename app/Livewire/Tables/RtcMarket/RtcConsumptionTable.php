<?php

namespace App\Livewire\tables\rtcMarket;

use App\Models\Project;
use App\Models\RtcConsumption;
use App\Models\SchoolRtcConsumption;
use App\Models\User;
use App\Traits\BatchTrait;
use App\Traits\ExportTrait;
use App\Traits\UITrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class RtcConsumptionTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    use UITrait;
    use BatchTrait;
    public bool $multiSort = true;
    public function setUp(): array
    {
        // $this->showCheckBox();
        if ($this->getBatch()) {
            $this->search = $this->getBatch();
        }
        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage(10)
                ->pageName('consumption')
                ->showRecordCount(),
        ];
    }
    public function actions($row): array
    {
        $user = User::find(auth()->user()->id);
        $project = Project::where('name', 'RTC Market')->first();
        $link = '/forms/' . str_replace(' ', '-', strtolower($project->name)) . '/rtc-consumption-form/edit/' . $row->id . '/' . $row->uuid;


        switch ($user->roles()->first()->name) {
            case 'admin':
                $link = "/admin" . $link;
                break;

            case 'staff':
                $link = "/staff" . $link;
                break;
            case 'manager':
                $link = "/manager" . $link;
                break;

            case 'monitor':
                $link = "/monitor" . $link;
                break;
        }

         // Define the user variable once to avoid repeated calls
        $user = auth()->user();

        // Use dedicated role checks
        $isAdmin = $user->hasAnyRole(['admin', 'manager']);
        $isStaff = $user->hasAnyRole('staff');

        // Logic for staff permission (Only their own records)
        $canEdit = $isAdmin || ($isStaff && $user->id === $row->user_id);

        return [
            Button::add('edit')
                ->can($canEdit)
                ->render(function () use ($link) {
                    // Using a simple return is faster than compiling a Blade view for a single button
                    return '<a href="' . $link . '" class="btn btn-warning btn-sm" title="Edit Record"><i class="bx bx-pen"></i></a>';
                })
        ];
    }
    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = RtcConsumption::query()->with(
            [
                'user' => fn($q) => $q->withTrashed(),
                'user.organisation',
            ]
        )->select([
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
            ->add('date_formatted', fn($model) => $model->date ? Carbon::parse($model->date)->format('d/m/Y') : null)
            ->add('crop')
            ->add('crop_cassava', fn($model) => $this->booleanUI($model->crop_cassava, $model->crop_cassava == 1))
            ->add('crop_potato', fn($model) => $this->booleanUI($model->crop_potato, $model->crop_potato == 1))
            ->add('crop_sweet_potato', fn($model) => $this->booleanUI($model->crop_sweet_potato, $model->crop_sweet_potato == 1))
            ->add('male_count')
            ->add('female_count')
            ->add('total', function ($model) {

                return ($model->male_count + $model->female_count) ?? 0;
            })
            ->add('number_of_households')
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
                return null;
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
            Column::action('')->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('#', 'rn')->sortable()->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('UUID', 'uuid')->searchable()->hidden(),
            Column::make('Entity ID', 'en_id')->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Entity Name', 'entity_name', 'entity_name')
                ->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),

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

            Column::make('Number of households', 'number_of_households', 'number_of_households')
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

            Filter::select('crop_cassava', 'crop_cassava')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })->optionLabel('name')->optionValue('value'),

            Filter::select('crop_potato', 'crop_potato')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })
                ->optionLabel('name')
                ->optionValue('value'),

            Filter::select('crop_sweet_potato', 'crop_sweet_potato')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })
                ->optionLabel('name')
                ->optionValue('value'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }
}
