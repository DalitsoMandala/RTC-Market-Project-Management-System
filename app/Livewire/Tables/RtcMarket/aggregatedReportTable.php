<?php

namespace App\Livewire\tables\rtcmarket;

use App\Jobs\ReportSummaryJob;
use App\Models\AggregatedReport;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\SystemReportData;
use App\Models\User;
use App\Traits\ExportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class aggregatedReportTable extends PowerGridComponent
{

    use WithExport;
    use ExportTrait;

    public $project;
    public $reporting_period;
    public $financial_year;
    public $organisation_id;
    public $indicator;
    public $disaggregation;
    public bool $withSortStringNumber = true;
    public string $sortField = 'aggregated_reports.indicator_id';

    // ─── Inline Edit State ────────────────────────────────────────────────────
    public ?int $editingId = null;   // ID of the row currently being edited
    public ?string $editValue = null; // Staged new value (before confirmation)

    public function setUp(): array
    {
        return [
            Header::make(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }


    /**
     * Confirmed: persist the new value to the database.
     * Triggered by the "Confirm" button inside the Alpine modal via $wire.confirmSave().
     */
    public function confirmSave(): void
    {
        if (is_null($this->editingId)) {
            return;
        }

        $report = AggregatedReport::findOrFail($this->editingId);

        // Validate: allow numeric or null values (adjust rules to your domain)
        $this->validate(
            ['editValue' => 'required|numeric'],
            ['editValue.required' => 'Value cannot be empty.', 'editValue.numeric' => 'Value must be a number.']
        );

        $report->update(['value' => $this->editValue]);

        $this->editingId = null;
        $this->editValue = null;

        // Re-render the table
        $this->dispatch('pg:eventRefresh-default');

        session()->flash('success', 'Report value updated successfully.');
    }


    // ─── Export ───────────────────────────────────────────────────────────────

    public $namedExport = 'report';

    #[On('export-report')]
    public function startExport()
    {
        if (!$this->checkReport()) {
            $this->dispatch('export-fail', message: 'Data is updating, please wait a few minutes.');
            return;
        }
        $this->namedExport = 'report';
        $this->execute($this->namedExport);
        $this->performExport();
    }

    #[On('export-report')]
    public function startProgressExport()
    {
        if (!$this->checkReport()) {
            $this->dispatch('export-fail', message: 'Data is updating, please wait a few minutes.');
            return;
        }
        $this->namedExport = 'summary';
        $this->execute($this->namedExport);
        $this->performExport();
    }

    public function checkReport()
    {
        $check = ReportStatus::where('status', 'completed')->exists();
        return $check ? true : false;
    }

    #[On('download-export')]
    public function downloadExport()
    {
        if (!Storage::exists('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx')) {
            return;
        }
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }


    // ─── Data Source ──────────────────────────────────────────────────────────

    public function datasource(): Builder
    {
        $query = AggregatedReport::query()
            ->with(['indicator', 'organisation', 'financialYear', 'reportingPeriod', 'indicatorDisaggregation'])
            ->leftJoin('indicators', 'indicators.id', '=', 'aggregated_reports.indicator_id')
            ->leftJoin('organisations', 'organisations.id', '=', 'aggregated_reports.organisation_id')
            ->leftJoin('financial_years', 'financial_years.id', '=', 'aggregated_reports.financial_year_id')
            ->leftJoin('reporting_periods', 'reporting_periods.id', '=', 'aggregated_reports.reporting_period_id')
            ->select([
                'aggregated_reports.*',
                'indicators.indicator_name as indicator_name',
                'indicators.indicator_no as indicator_no',
                'organisations.name as organisation_name',
                'financial_years.number as financial_year',
            ]);

        $user = User::find(auth()->user()->id);
        if ($user->hasAnyRole('external')) {
            $query->where('aggregated_reports.organisation_id', $user->organisation->id);
        }

        return $query->orderBy('aggregated_reports.crop', 'asc')
            ->orderBy('aggregated_reports.indicator_id', 'asc');
    }


    // ─── Filters ──────────────────────────────────────────────────────────────

    private function hasFilters(): bool
    {
        return !is_null($this->organisation_id) ||
            !is_null($this->reporting_period) ||
            !is_null($this->financial_year) ||
            !is_null($this->disaggregation) ||
            !is_null($this->indicator) ||
            !is_null($this->crop);
    }

    public function filters(): array
    {
        return [
            Filter::select('indicator_name', 'indicators.id')
                ->dataSource(function () {
                    return Indicator::distinct()->get()->map(function ($indicator) {
                        return [
                            'id'             => $indicator->id,
                            'number'         => $indicator->indicator_no,
                            'indicator_name' => $indicator->indicator_name,
                            'name'           => "({$indicator->indicator_no}) {$indicator->indicator_name}",
                        ];
                    });
                })
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('name', 'system_report_data.name')
                ->dataSource(IndicatorDisaggregation::select(['name'])->distinct()->get())
                ->optionLabel('name')
                ->optionValue('name'),

            Filter::select('crop')
                ->dataSource([
                    ['name' => 'All crops', 'value' => 'null'],
                    ['name' => 'Cassava',    'value' => 'Cassava'],
                    ['name' => 'Potato',     'value' => 'Potato'],
                    ['name' => 'Sweet potato', 'value' => 'Sweet potato'],
                ])
                ->optionLabel('name')
                ->optionValue('value')
                ->builder(function (Builder $query, $value) {
                    if ($value === 'null') {
                        $query->whereNull('system_reports.crop');
                    } else {
                        $query->where('system_reports.crop', $value);
                    }
                }),

            Filter::select('financial_year', 'financial_years.id')
                ->dataSource(
                    FinancialYear::query()
                        ->whereHas('project', fn($q) => $q->where('name', 'RTC Market'))
                        ->get()
                        ->map(fn($year) => ['number' => $year->number, 'name' => 'Year ' . $year->number, 'id' => $year->id])
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('report_period', 'system_reports.reporting_period_id')
                ->dataSource(function () {
                    return ReportingPeriodMonth::with('reportingPeriod')
                        ->whereHas('reportingPeriod', fn($q) => $q->where('name', 'QUARTERLY'))
                        ->distinct()
                        ->get()
                        ->map(function ($months) {
                            $unspecified = $months->start_month == $months->end_month
                                ? $months->start_month
                                : "{$months->start_month}-{$months->end_month}";
                            return ['id' => $months->id, 'name' => $unspecified];
                        });
                })
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('organisation_name', 'organisations.id')
                ->dataSource(Organisation::select(['name', 'id'])->distinct()->get())
                ->optionLabel('name')
                ->optionValue('id'),
        ];
    }


    // ─── Relation Search ──────────────────────────────────────────────────────

    public function relationSearch(): array
    {
        return [
            'indicator'    => ['indicator_name', 'indicator_no'],
            'organisation' => ['name'],
            'financialYear' => ['number'],
        ];
    }


    // ─── Fields ───────────────────────────────────────────────────────────────

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('number', fn($model) => $model->indicator->indicator_no ?? null)
            ->add('indicator_name', function ($model) {
                $indicatorName   = $model->indicator->indicator_name ?? null;
                $indicatorNumber = $model->indicator->indicator_no ?? null;
                return '(' . $indicatorNumber . ') ' . $indicatorName;
            })
            ->add('name', fn($model) => $model->indicatorDisaggregation->name ?? null)
            ->add('project', fn($model) => $model->project->name ?? null)

            // ── Value cell: input when editing, styled span otherwise ──────────
            ->add('value', function ($model) {

                if ($this->editingId === $model->id) {

                    $error = $this->getErrorBag()->first('editValue');

                    return new HtmlString("
                    <div>
                        <input
                            type='number'
                            wire:model.live='editValue'
                            class='form-control form-control-sm'
                            style='min-width:100px; max-width:140px;'
                            autofocus
                        />

                        " . ($error
                        ? "<div class='text-danger small mt-1'>{$error}</div>"
                        : ""
                    ) . "
                    </div>
                ");
                }

                return new HtmlString("
                <span class='fw-bolder'>{$model->value}</span>
            ");
            })

            ->add('report_period', function ($model) {
                if (!$model->reportingPeriod) return null;
                return $model->reportingPeriod->start_month . ' - ' . $model->reportingPeriod->end_month;
            })
            ->add('financial_year', fn($model) => $model->financialYear->number ?? null)
            ->add('crop', fn($model) => $model->crop ?? 'All crops')
            ->add('organisations', fn($model) => $model->organisation->name ?? null)
            ->add('updated_at', fn($model) => $model->updated_at->format('Y-m-d H:i:s') ?? null);
    }


    // ─── Columns ──────────────────────────────────────────────────────────────

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->hidden()->visibleInExport(false),
            Column::make('Indicator Name', 'indicator_name')
                ->headerAttribute(styleAttr: 'min-width:350px;')
                ->bodyAttribute(styleAttr: 'white-space:wrap')
                ->searchable()
                ->sortable(),
            Column::make('Disaggregation', 'name')->sortable()->searchable(),
            Column::make('Project', 'project')->hidden()->visibleInExport(true),
            Column::make('Reporting period', 'report_period')->searchable(),
            Column::make('Organisation', 'organisations', 'organisation_name')->sortable()->searchable(),
            Column::make('Project year', 'financial_year')->sortable(),
            Column::make('Enterprise', 'crop', 'system_reports.crop')->searchable(),
            Column::make('Value', 'value')->sortable(),
            Column::action('Actions'),
        ];
    }


    // ─── Action Buttons ───────────────────────────────────────────────────────
    //
    // PowerGrid v5 uses ->dispatch() to fire a Livewire event.
    // The component listens with #[On] and receives the dispatched payload.

    public function actions(AggregatedReport $row): array
    {
        // While editing THIS row: show Save + Cancel
        if ($this->editingId === $row->id) {
            return [
                Button::add('save')
                    ->slot('<i class="bi bi-check-lg"></i> Save')
                    ->class('btn btn-success btn-sm')
                    // Fires 'pg:requestSave' on the component carrying the row id.
                    // #[On('pg:requestSave')] below handles it.
                    ->dispatch('pg:requestSave', ['id' => $row->id]),

                Button::add('cancel')
                    ->slot('<i class="bi bi-x-lg"></i> Cancel')
                    ->class('btn btn-secondary btn-sm ms-1')
                    // Fires 'pg:cancelEdit'; no payload needed.
                    ->dispatch('pg:cancelEdit', []),
            ];
        }

        // Default state: show Edit
        return [
            Button::add('edit')
                ->slot('<i class="bi bi-pencil"></i> Edit')
                ->class('btn btn-primary btn-sm')
                // Fires 'pg:startEdit' carrying the row id.
                ->dispatch('pg:startEdit', ['id' => $row->id]),
        ];
    }

    // ─── Event Listeners (replaces wireClick) ─────────────────────────────────

    #[On('pg:startEdit')]
    public function startEdit(int $id): void
    {
        $report = AggregatedReport::findOrFail($id);
        $this->editingId = $id;
        $this->editValue = $report->value;
    }

    #[On('pg:cancelEdit')]
    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editValue = null;
    }

    /**
     * Stage the save and open the Alpine confirmation modal.
     * The actual DB write only happens in confirmSave() after the user confirms.
     */
    #[On('pg:requestSave')]
    public function requestSave(int $id): void
    {
        $this->editingId = $id; // keep input visible while modal is open
        $this->dispatch('open-confirm-modal');
    }
}