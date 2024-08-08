<?php

namespace App\Livewire\tables;

use App\Models\AssignedTarget;
use App\Models\Indicator;
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
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class AssignedTargetTable extends PowerGridComponent
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
                ->showPerPage()->pageName('assigned-targets')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return AssignedTarget::query()->with(['organisation', 'final_target']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_target_id')
            ->add('indicator', function ($model) {
                $indicatorId = $model->final_target->indicator_id;
                $indicator = Indicator::find($indicatorId);
                return $indicator->indicator_name;
            })
            ->add('organisation_id')
            ->add('organisation', fn($model) => $model->organisation->name)
            ->add('target_value')
            ->add('current_value')
            ->add('detail')
            ->add('progress', function ($model) {
                $current = $model->current_value;
                $target = $model->target_value;
                $progress = ($current / $target) * 100;


                return '
<div class="mb-1 text-center text-primary fw-medium">' . floor($progress) . '%</div>
<div class="progress bg-primary-subtle progress-sm">
<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $progress . '%"></div>
</div>

';
            })
            ->add('type')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator'),
            Column::make('Organisation', 'organisation'),


            Column::make('Current value', 'current_value')
                ->sortable()
                ->searchable(),
            Column::make('Target value', 'target_value')
                ->sortable()
                ->searchable(),
            // Column::make('Detail', 'detail')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Type', 'type')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Progress', 'progress')
                ->sortable(),



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
