<?php

namespace App\Livewire\tables\rtcMarket;

use App\Models\Crop;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use App\Models\User;
use App\Traits\ExportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ReportTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;

    public bool $withSortStringNumber = true;
    public string $sortField = 'system_report_data.value';
    public $namedExport = 'report';

    public function setUp(): array
    {
        return [
            Header::make()
                ->includeViewOnTop('components.report-header'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    #[On('export-report')]
    public function startExport()
    {
        if (! $this->checkReport()) {
            $this->dispatch('export-fail', message: 'Data is updating, please wait a few minutes.');
            return;
        }
        $this->namedExport = 'report';
        $this->execute($this->namedExport);
        $this->performExport();
    }

    #[On('report-updated')]
    public function completedReport()
    {
        //
    }

    #[On('export-report')]
    public function startProgressExport()
    {
        if (! $this->checkReport()) {
            $this->dispatch('export-fail', message: 'Data is updating, please wait a few minutes.');
            return;
        }
        $this->namedExport = 'summary';
        $this->execute($this->namedExport);
        $this->performExport();
    }

    public function checkReport(): bool
    {
        return ReportStatus::where('status', 'completed')->exists();
    }

    #[On('download-export')]
    public function downloadExport()
    {
        $filePath = 'public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx';

        if (! Storage::exists($filePath)) {
            return;
        }

        return Storage::download($filePath);
    }

  public function datasource(): Builder
{
    $query = SystemReportData::query()
        ->with([
            'systemReport.indicator',
            'systemReport.organisation',
            'systemReport.financialYear',
            'systemReport.reportingPeriodMonth',
            'systemReport.project',
        ])
        ->join(
            'system_reports',
            'system_reports.id',
            '=',
            'system_report_data.system_report_id'
        )
        ->leftJoin(
            'indicators',
            'indicators.id',
            '=',
            'system_reports.indicator_id'
        )
        ->leftJoin(
            'organisations',
            'organisations.id',
            '=',
            'system_reports.organisation_id'
        )
        ->leftJoin(
            'financial_years',
            'financial_years.id',
            '=',
            'system_reports.financial_year_id'
        )
        ->select([
            'system_report_data.*',
            'indicators.indicator_name as indicator_name',
            'indicators.indicator_no as indicator_no',
            'organisations.name as organisation_name',
            'financial_years.number as financial_year',
        ]);

    $user = User::find(auth()->id());

    if ($user && $user->hasAnyRole('external')) {
        $query->where(
            'system_reports.organisation_id',
            $user->organisation->id ?? null
        );
    }

    return $query;
}

    public function filters(): array
    {
        return [
            Filter::select('indicator_name', 'indicators.id')
                ->dataSource(function () {
                    return Indicator::distinct()->get()->map(function ($indicator) {
                        return [
                            'id' => $indicator->id,
                            'number' => $indicator->indicator_no,
                            'indicator_name' => $indicator->indicator_name,
                            'name' => "({$indicator->indicator_no}) {$indicator->indicator_name}",
                        ];
                    });
                })->optionLabel('name')
                ->optionValue('id'),

            Filter::select('name', 'system_report_data.name')
                ->dataSource(IndicatorDisaggregation::select(['name'])->distinct()->get())
                ->optionLabel('name')
                ->optionValue('name'),

            Filter::select('crop')
                ->dataSource([
                    ['name' => 'All crops', 'value' => 'null'],
                    ['name' => 'Cassava', 'value' => 'Cassava'],
                    ['name' => 'Potato', 'value' => 'Potato'],
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
                        ->whereHas('project', function ($query) {
                            $query->where('name', 'RTC Market');
                        })
                        ->get()
                        ->map(function ($year) {
                            return [
                                'number' => $year->number,
                                'name' => 'Year ' . $year->number,
                                'id' => $year->id,
                            ];
                        })
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('report_period', 'system_reports.reporting_period_month_id')
                ->dataSource(function () {
                    return ReportingPeriodMonth::with('reportingPeriod')
                        ->whereHas('reportingPeriod', function ($query) {
                            $query->where('name', 'QUARTERLY');
                        })
                        ->distinct()
                        ->get()
                        ->map(function ($months) {
                            $start_month = $months->start_month;
                            $end_month = $months->end_month;
                            $unspecified = $months->start_month == $months->end_month
                                ? $months->start_month
                                : "{$start_month}-{$end_month}";

                            return [
                                'id' => $months->id,
                                'name' => $unspecified,
                            ];
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

    public function relationSearch(): array
    {
        return [
            'systemReport.indicator' => ['indicator_name', 'indicator_no'],
            'systemReport.organisation' => ['name'],
            'systemReport.financialYear' => ['number'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('number', function ($model) {
                return $model->systemReport->indicator->indicator_no ?? null;
            })
            ->add('indicator_name', function ($model) {
                $indicatorName = $model->systemReport->indicator->indicator_name ?? null;
                $indicatorNumber = $model->systemReport->indicator->indicator_no ?? null;
                return '(' . $indicatorNumber . ') ' . $indicatorName;
            })
            ->add('name', function ($model) {
                return $model->name ?? null;
            })
            ->add('project', function ($model) {
                return $model->systemReport->project->name ?? null;
            })
            ->add('value', function ($model) {
                return "<span class='fw-bolder'>{$model->value}</span>";
            })
            ->add('report_period', function ($model) {
                if (! $model->systemReport->reportingPeriodMonth) {
                    return null;
                }

                $start_month = $model->systemReport->reportingPeriodMonth->start_month;
                $end_month = $model->systemReport->reportingPeriodMonth->end_month;

                return $start_month . ' - ' . $end_month;
            })
            ->add('financial_year', function ($model) {
                return $model->systemReport->financialYear->number ?? null;
            })
            ->add('crop', function ($model) {
                return $model->systemReport->crop ?? 'All crops';
            })
            ->add('organisations', function ($model) {
                return $model->systemReport->organisation->name ?? null;
            })
            ->add('updated_at', function ($model) {
                return optional($model->updated_at)->format('Y-m-d H:i:s');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->hidden()->visibleInExport(false),

            Column::make('Indicator Name', 'indicator_name', 'indicators.indicator_name')
                ->headerAttribute(styleAttr: "min-width:350px;")
                ->bodyAttribute(styleAttr: "white-space:wrap")
                ->searchable()
                ->sortable(),

            Column::make('Disaggregation', 'name', 'system_report_data.name')
                ->sortable()
                ->searchable(),

            Column::make('Project', 'project')->hidden()->visibleInExport(true),

            Column::make('Reporting period', 'report_period', 'system_reports.reporting_period_month_id')
                ->searchable()
                ->sortable(),

            Column::make('Organisation', 'organisation_name', 'organisations.name')
                ->sortable()
                ->searchable(),

            Column::make('Project year', 'financial_year', 'financial_years.number')
                ->sortable(),

            Column::make('Enterprise', 'crop', 'system_reports.crop')
                ->searchable()
                ->sortable(),

            Column::make('Value', 'value', 'system_report_data.value')
                ->sortable(),
        ];
    }
}
