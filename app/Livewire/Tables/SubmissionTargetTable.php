<?php

namespace App\Livewire\tables;

use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\SubmissionTarget;
use Illuminate\Support\Facades\DB;
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

final class SubmissionTargetTable extends PowerGridComponent
{
    use WithExport;

    public string $sortField = 'updated_at';

    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
            Exportable::make('submission_targets')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return SubmissionTarget::query()->with([
            'financialYear',
            'indicator'
        ])->join('financial_years', function ($join) {
            $join->on('submission_targets.financial_year_id', '=', 'financial_years.id');
        })->select([
            'submission_targets.*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
            'financial_years.number as year',

        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('financial_year_id')
            ->add('financial_year', fn($model) => 'Year ' . $model->financialYear->number)
            ->add('indicator_id')
            ->add('indicator', fn($model) => $model->indicator->indicator_name)
            ->add('target_name')
            ->add('target_value', function ($model) {

                $formatted = number_format($model->target_value, 2, '.', '');

                // Remove trailing .00 if necessary
                if (strpos($formatted, '.00') !== false) {
                    $formatted = rtrim($formatted, '0');
                    $formatted = rtrim($formatted, '.');
                }

                $model->target_value = $formatted; // Output: 2
                if ($model->target_name == "Total (% Percentage)") {
                    return $model->target_value . ' %';
                }
                return $model->target_value;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn'),
            Column::make('Financial year', 'financial_year', 'financial_years.number')->sortable(),
            Column::make('Indicator', 'indicator'),
            Column::make('Disaggregation', 'target_name'),
            Column::make('Value', 'target_value'),



        ];
    }

    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function filters(): array
    {
        return [];
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
