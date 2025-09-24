<?php

namespace App\Livewire\tables;

use Faker\Core\File;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\IndicatorDisaggregation;
use Illuminate\Support\Carbon;
use App\Models\SubmissionTarget;
use Database\Seeders\DisaggregationSeeder;
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
 public bool $multiSort = true;
    public function setUp(): array
    {
        //$this->showCheckBox();

        return [

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
            'indicator',
            'organisationTargets'
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
            ->add('indicator', fn($model) => "({$model->indicator->indicator_no}) " . $model->indicator->indicator_name)
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
                    return $model->target_value . '%';
                }
                return $model->target_value;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn')->sortable(),
            Column::make('Financial year', 'financial_year', 'financial_years.number')->sortable(),
            Column::make('Indicator', 'indicator'),
            Column::make('Disaggregation', 'target_name'),
            Column::make('Value', 'target_value')->bodyAttribute('fw-bold'),
            Column::action('Action')


        ];
    }

    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function filters(): array
    {
        return [
          Filter::select('financial_year', 'financial_years.number')
            ->dataSource(FinancialYear::get()->map(function ($year) {
                return [
                    'number' => $year->number,
                    'name' => 'Year ' . $year->number,
                ];
            }))
            ->optionLabel('name')
            ->optionValue('number'),

            Filter::select('indicator', 'indicator_id')
                ->dataSource(Indicator::get()->map(function ($indicator) {
                    return [
                        'id' => $indicator->id,
                        'indicator_name' => "({$indicator->indicator_no}) " . $indicator->indicator_name,
                    ];
                }))
                ->optionLabel('indicator_name')
                ->optionValue('id'),

                Filter::select('target_name', 'target_name')
                ->dataSource(IndicatorDisaggregation::select(['name'])->distinct()->get())
                ->optionLabel('name')
                ->optionValue('name'),
        ];
    }

    #[On('edit')]
    public function edit($rowId): void
    {
        $this->dispatch('show-targets', rowId: $rowId);
    }




    public function actions($row): array
    {

        return [
            Button::add('edit')
                ->slot('<i class="fa fa-eye"></i>')
                ->id()
                ->tooltip('View Details')
                ->class('btn btn-warning btn-sm custom-tooltip')
                ->dispatch('edit', ['rowId' => $row])
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
