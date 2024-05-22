<?php

namespace App\Livewire;

use App\Models\Cgiar_Project;
use App\Models\Indicator;
use Illuminate\Database\Eloquent\Builder as ModelBuilder;
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
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class IndicatorTable extends PowerGridComponent
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

    public function datasource(): ?ModelBuilder
    {
        return Indicator::query()->with('project');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_no')
            ->add('indicator_no_bold', function ($model) {

                return '<b>' . $model->indicator_no . '</b>';
            })

            ->add('name_link', function ($model) {

                return '<a  href="' . route('cip-internal-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
            })
            ->add('project_id')
            ->add('project_name', fn($model) => $model->project->name)
            ->add('cgiar_project', function ($model) {
                return Cgiar_Project::find($model->project_id)->name ?? null;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),

            Column::make('Indicator #', 'indicator_no_bold')
                ->sortable()
                ->searchable(),
            Column::make('Indicator', 'name_link', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Project name', 'project_name'),
            Column::make('Cgiar project', 'cgiar_project'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
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
                ->class('btn btn-primary')
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