<?php

namespace App\Livewire\tables\rtcMarket;

use App\Jobs\ReportSummaryJob;
use App\Models\Crop;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\ResponsiblePerson;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use App\Models\User;
use App\Traits\ExportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
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

final class ReportTable extends PowerGridComponent
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
    public string $sortField = 'system_reports.indicator_id';


    public function setUp(): array
    {
        //  $this->showCheckBox();
        $timestamp = Carbon::now();
        return [
            Header::make()
                // ->showSearchInput()
                ->includeViewOnTop('components.report-header'),
            // ->includeViewOnBottom('components.import-button'),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }



    public $namedExport = 'report';

    #[On('export-report')]
    public function startExport()
    {
         if (!$this->checkReport()) {
            $this->dispatch('export-fail', message:'Data is updating, please wait a few minutes.');
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
            $this->dispatch('export-fail', message:'Data is updating, please wait a few minutes.');
            return;
        }
        $this->namedExport = 'summary';
        $this->execute($this->namedExport);
        $this->performExport();
        // Bus::dispatch(new ReportSummaryJob(auth()->user()));
    }

    public function checkReport()
    {

        $check = ReportStatus::where('status', 'completed')->exists();

        if ($check) {
            return true;
        }
        return false;
    }
    #[On('download-export')]
    public function downloadExport()
    {

        if (!Storage::exists('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx')) {
            return;
        }
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function datasource(): Builder
    {
        // Start building the query for SystemReportData and eager load systemReport

        $query = SystemReportData::query()
            ->with(['systemReport', 'systemReport.indicator', 'systemReport.organisation', 'systemReport.financialYear'])
            ->join('system_reports', 'system_reports.id', '=', 'system_report_data.system_report_id')
            ->leftJoin('indicators', 'indicators.id', '=', 'system_reports.indicator_id')
            ->leftJoin('organisations', 'organisations.id', '=', 'system_reports.organisation_id')
            ->leftJoin('financial_years', 'financial_years.id', '=', 'system_reports.financial_year_id')
            ->select([
                'system_report_data.*',
                'indicators.indicator_name as indicator_name',
                'indicators.indicator_no as indicator_no',
                'organisations.name as organisation_name',
                'financial_years.number as financial_year',

            ]);

        if (request()->get('filters')['crop'] ?? null === 'null') {
            dd('sss');
        }

        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('external')) {
            $query->where('system_reports.organisation_id', $user->organisation->id);
        }


        return $query->orderBy('system_reports.crop', 'asc')
            ->orderBy('system_reports.indicator_id', 'asc');
    }

    /**
     * Check if any filters are set.
     */
    private function hasFilters(): bool
    {
        return !is_null($this->organisation_id) ||
            !is_null($this->reporting_period) ||
            !is_null($this->financial_year) ||
            !is_null($this->disaggregation) ||
            !is_null($this->indicator) ||
            !is_null($this->crop);
    }

    /**
     * Apply filters to the query.
     */

    public function filters(): array
    {
        return [
            Filter::select('indicator_name', 'indicators.id')
                ->dataSource(function () {
                    $indicators = Indicator::distinct()->get();
                    return $indicators->map(function ($indicator) {
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
                                'name'   => 'Year ' . $year->number,
                                'id'     => $year->id,
                            ];
                        })
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('report_period', 'system_reports.reporting_period_id')
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
                            $unspecified = $months->start_month == $months->end_month ? $months->start_month : "{$start_month}-{$end_month}";

                            return [
                                'id'   => $months->id,
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
                // Handle null for systemReport and indicator
                return $model->systemReport->indicator->indicator_no ?? null;
            })
            ->add('indicator_name', function ($model) {
                // Handle null for systemReport and indicator
                $indicatorName = $model->systemReport->indicator->indicator_name ?? null;
                $indicatorNumber = $model->systemReport->indicator->indicator_no ?? null;
                return  '(' . $indicatorNumber . ') ' . $indicatorName;
            })
            ->add('name', function ($model) {

                return $model->name ?? null;
            })
            ->add('project', function ($model) {
                // Handle null for systemReport and project
                return $model->systemReport->project->name ?? null;
            })
            ->add('value', function ($model) {
                return $model->value ?? null;
            })
            ->add('report_period', function ($model) {
                // Handle null for reportingPeriod
                if (!$model->systemReport->reportingPeriod) {
                    return null;
                }

                $start_month = $model->systemReport->reportingPeriod->start_month;
                $end_month = $model->systemReport->reportingPeriod->end_month;

                return $start_month . ' - ' . $end_month;
            })
            ->add('financial_year', function ($model) {
                // Handle null for financialYear
                return $model->systemReport->financialYear->number ?? null;
            })

            ->add('crop', function ($model) {
                // Handle null for financialYear
                return $model->systemReport->crop ?? 'All crops';
            })
            ->add('organisations', function ($model) {
                // Handle null for organisation
                return $model->systemReport->organisation->name ?? null;
            })
            ->add('updated_at', function ($model) {
                return $model->updated_at->format('Y-m-d H:i:s') ?? null;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->hidden()->visibleInExport(false),
            // Column::make('Indicator #', 'number', 'indicator_no')
            //     ->searchable(),
            Column::make('Indicator Name', 'indicator_name')
                ->headerAttribute(styleAttr: "min-width:350px;")
                ->bodyAttribute(styleAttr: "white-space:wrap")
                ->searchable()
                ->sortable(),
            Column::make('Disaggregation', 'name')
                ->sortable()
                ->searchable(),


            Column::make('Value', 'value')
                ->sortable(),


            Column::make('Project', 'project')->hidden()->visibleInExport(true),
            Column::make('Reporting period', 'report_period')->searchable(),
            Column::make('Organisation', 'organisations', 'organisation_name')->sortable()->searchable(),
            Column::make('Project year', 'financial_year')->sortable(),
            Column::make('Enterprise', 'crop', 'system_reports.crop')->searchable(),
        ];
    }


    /*
     * public function actionRules($row): array
     * {
     *    return [
     *         // Hide button edit for ID 1
     *         Rule::button('edit')
     *             ->when(fn($row) => $row->id === 1)
     *             ->hide(),
     *     ];
     * }
     */
}
