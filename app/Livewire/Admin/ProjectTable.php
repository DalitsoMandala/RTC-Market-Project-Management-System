<?php

namespace App\Livewire\admin;

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProjectTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //$this->showCheckBox();

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
        return Project::with([
            'reportingPeriod',
            'indicators'
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('duration')
            ->add('duration_formatted', fn($model) => $model->duration . ' years')

            ->add('start_date_formatted', fn($model) => Carbon::parse($model->start_date)->format('d/m/Y'))
            ->add('is_active')
            ->add('is_active_formatted', fn($model) => $model->is_active ? 'Yes' : 'No')
            ->add('cgiar_project_id')
            ->add('cgiar_project_id_formatted', fn($model) => $model->cgiarProject->name)
            ->add('reporting_period_id')
            ->add('reporting_period_id_formatted', function ($model) {
                return $model->reportingPeriod->name;

            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Duration', 'duration_formatted', 'duration')
                ->sortable()
                ->searchable(),

            Column::make('Start date', 'start_date_formatted', 'start_date')
                ->sortable(),

            Column::make('Is active', 'is_active_formatted', 'is_active')
                ->sortable()
                ->searchable(),

            Column::make('Cgiar project ', 'cgiar_project_id_formatted', 'cgiar_project_id')->searchable(),
            Column::make('Reporting period type', 'reporting_period_id_formatted', 'reporting_period_id')->searchable(),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('start_date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }
    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning')
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name' => 'view-edit-modal'
                ])
        ];
    }

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => true)
                ->disable(),
        ];
    }

}
