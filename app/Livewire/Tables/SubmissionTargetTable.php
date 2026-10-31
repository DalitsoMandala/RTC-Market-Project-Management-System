<?php

namespace App\Livewire\tables;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\SubmissionTarget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SubmissionTargetTable extends PowerGridComponent
{
    use WithExport;

    private const PERCENT_TARGET = 'Total (% Percentage)';

    // Qualified, because the joined financial_years table also has updated_at.
    public string $sortField     = 'submission_targets.updated_at';
    public string $sortDirection = 'desc';
    public bool $multiSort       = true;

    public function setUp(): array
    {
        return [
            Header::make(),
            Footer::make()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return SubmissionTarget::query()
            ->with('indicator')
            ->join('financial_years', 'submission_targets.financial_year_id', '=', 'financial_years.id')
            ->whereHas('indicator', fn ($query) => $query->where('indicators.is_active', 1))
            ->select([
                'submission_targets.*',
                'financial_years.number as year',
                DB::raw('ROW_NUMBER() OVER (ORDER BY submission_targets.id) AS rn'),
            ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('financial_year', fn ($model) => 'Year ' . $model->year)
            ->add('indicator', fn ($model) => "({$model->indicator->indicator_no}) {$model->indicator->indicator_name}")
            ->add('target_name')
            ->add('target_value', fn ($model) => $this->formatValue($model->target_value, $model->target_name));
    }

    public function columns(): array
    {
        return [
            Column::action(''),
            Column::make('#', 'rn')->sortable(),
            Column::make('Financial year', 'financial_year', 'financial_years.number')->sortable(),
            Column::make('Indicator', 'indicator'),
            Column::make('Disaggregation', 'target_name'),
            Column::make('Value', 'target_value')->bodyAttribute('fw-bold'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('financial_year', 'financial_years.number')
                ->dataSource(
                    FinancialYear::get()->map(fn ($year) => [
                        'number' => $year->number,
                        'name'   => 'Year ' . $year->number,
                    ])
                )
                ->optionLabel('name')
                ->optionValue('number'),

            Filter::select('indicator', 'indicator_id')
                ->dataSource(
                    Indicator::where('is_active', 1)->get()->map(fn ($indicator) => [
                        'id'             => $indicator->id,
                        'indicator_name' => "({$indicator->indicator_no}) {$indicator->indicator_name}",
                    ])
                )
                ->optionLabel('indicator_name')
                ->optionValue('id'),

            Filter::select('target_name', 'target_name')
                ->dataSource(IndicatorDisaggregation::select('name')->distinct()->get())
                ->optionLabel('name')
                ->optionValue('name'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="fa fa-eye"></i>')
                ->id()
                ->tooltip('View Details')
                ->class('btn btn-warning btn-sm custom-tooltip')
                ->can(auth()->user()?->hasRole('admin') ?? false)
                ->dispatch('modify-targets', ['rowId' => $row->id]),
        ];
    }



    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    /**
     * 12.50 -> "12.5", 3.00 -> "3", percentage target gets a "%" suffix.
     */
    private function formatValue(mixed $value, ?string $targetName): string
    {
        $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return $targetName === self::PERCENT_TARGET ? $formatted . '%' : $formatted;
    }
}
