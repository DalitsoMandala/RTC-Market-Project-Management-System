<?php

namespace App\Livewire\tables\rtcMarket;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use App\Models\User;
use App\Traits\ExportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

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
    public $crop;
    public bool $withSortStringNumber = true;
    //  public string $sortField = 'system_reports.crop';
    public $nameOfTable = 'Reporting Table';
    public $descriptionOfTable = 'Generate report';
    public function setUp(): array
    {
        //  $this->showCheckBox();
        $timestamp = Carbon::now();
        return [
            Header::make()
                ->showSearchInput()
                ->includeViewOnTop('components.export-component'),
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
        $this->execute($this->namedExport);
        $this->performExport();
    }


    #[On('download-export')]
    public function downloadExport()
    {
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

        // Apply filters if any are provided
        if ($this->hasFilters()) {
            $this->applyFilters($query);
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
    private function applyFilters(Builder $query): void
    {
        // Filter by disaggregation if set
        if (!is_null($this->disaggregation)) {
            $query->where('system_report_data.name', $this->disaggregation);
        }

        // Get indicators associated with the organisation if organisation_id is set
        $indicators = [];
        if (!is_null($this->organisation_id)) {
            $indicators = ResponsiblePerson::where('organisation_id', $this->organisation_id)
                ->pluck('indicator_id')
                ->toArray();
        }

        // Apply filters to the systemReport relationship
        $query->whereHas('systemReport', function ($query) use ($indicators) {
            if (!is_null($this->organisation_id)) {
                $query->where('organisation_id', $this->organisation_id);
            }

            if (!is_null($this->financial_year)) {
                $query->where('financial_year_id', $this->financial_year);
            }

            if (!is_null($this->reporting_period)) {
                $query->where('reporting_period_id', $this->reporting_period);
            }

            if (!empty($indicators)) {
                $this->applyIndicatorFilter($query, $indicators);
            }

            if (!is_null($this->crop)) {
                $query->where('crop', $this->crop);
            } else {
                $query->whereNull('crop');
            }
        });
    }

    /**
     * Apply indicator filter to the query.
     */
    private function applyIndicatorFilter(Builder $query, array $indicators): void
    {
        if (!is_null($this->indicator)) {
            // Filter indicators to match the specific indicator if provided
            $query->whereIn('indicator_id', array_filter($indicators, fn($value) => $value === intval($this->indicator)));
        } else {
            // Otherwise, filter by all indicators associated with the organisation
            $query->whereIn('indicator_id', $indicators);
        }
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
                return $model->systemReport->indicator->indicator_name ?? null;
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
            Column::make('Indicator #', 'number', 'indicator_no')
                ->searchable(),
            Column::make('Indicator Name', 'indicator_name')
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

    #[On('filtered-data')]
    public function getData($data)
    {
        //   $this->project = $data['project_id'];
        $this->indicator = $data['indicator'];
        // $this->start_date = $data['start_date'];
        // $this->end_date = $data['end_date'];
        $this->reporting_period = $data['reporting_period'];
        $this->financial_year = $data['financial_year'];
        $this->organisation_id = $data['organisation_id'];
        $this->disaggregation = $data['disaggregation'];
        $this->crop = $data['crop'];
    }

    #[On('reset-filters')]
    public function resetData()
    {
        $this->reset('organisation_id', 'reporting_period', 'financial_year', 'indicator', 'disaggregation', 'project', 'crop');
        $this->resetPage();
        $this->refresh();
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
