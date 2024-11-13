<?php

namespace App\Livewire\admin;

use App\Models\IndicatorTarget;
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

final class IndicatorTargetTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {


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
        return IndicatorTarget::query()->with('details');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_id')
            ->add('indicator', fn($model) => $model->indicator->indicator_name)
            ->add('financial_year_id')
            ->add('financial_year', fn($model) => $model->financialYear->number)
            ->add('project_id')
            ->add('project', fn($model) => $model->project->name)
            ->add('target_value')
            ->add('baseline_value')
            ->add('type')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator'),
            Column::make('Financial year', 'financial_year'),
            Column::make('Project', 'project'),

            Column::make('Target value', 'target_value')
                ->sortable()
                ->searchable(),

            Column::make('Baseline value', 'baseline_value')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable(),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [

        ];
    }

    public function relationSearch(): array
    {
        return [

            'indicator' => [ // relationship on dishes model
                'indicator_name', // column enabled to search

            ],

            'project' => [ // relationship on dishes model
                'name', // column enabled to search

            ],
            'financialYear' => [ // relationship on dishes model
                'number', // column enabled to search

            ],





        ];
    }


    #[\Livewire\Attributes\On('refresh')]
    public function edit(): void
    {

    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning goUp')
                ->dispatch('show-form', [
                    'rowId' => $row->id,
                    'indicator_id' => $row->indicator_id
                ])
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
