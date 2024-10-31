<?php

namespace App\Livewire\tables\rtcMarket;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use Livewire\Attributes\On;
use App\Models\SystemReport;
use Illuminate\Support\Carbon;
use App\Models\SystemReportData;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\DB;
use App\Models\ReportingPeriodMonth;
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

final class ReportTable extends PowerGridComponent
{
    use WithExport;

    public $project;
    public $reporting_period;
    public $financial_year;
    public $organisation_id;

    public $indicator;

    public $disaggregation;

    public bool $withSortStringNumber = true;

    public function setUp(): array
    {
        //  $this->showCheckBox();
        $timestamp = Carbon::now();
        return [
            Exportable::make('CDMS Report_'),
            Header::make()->showSearchInput()->includeViewOnTop('components.export-component'),
            Footer::make()
                ->showPerPage(5)
                ->showRecordCount(),
        ];
    }

    // public function datasource(): Builder
    // {
    //     // Start building the query for SystemReportData and eager load systemReport
    //     $query = SystemReportData::query()->with('systemReport');



    //     // Check if all three are provided, or if some are null
    //     if (!is_null($this->organisation_id) && !is_null($this->reporting_period) && !is_null($this->financial_year) && !is_null($this->disaggregations)) {
    //         // Get the indicators associated with the organisation from ResponsiblePerson
    //         $indicators = ResponsiblePerson::where('organisation_id', $this->organisation_id)->pluck('indicator_id');

    //         // Apply the filters to the query
    //         $query->whereHas('systemReport', function ($query) use ($indicators) {
    //             $query->where('organisation_id', $this->organisation_id)
    //                 ->whereIn('indicator_id', $indicators)
    //                 ->where('financial_year_id', $this->financial_year)
    //                 ->where('reporting_period_id', $this->reporting_period);
    //         });
    //     }
    //     // If only some of the filters are provided (one or two), return an empty result

    //     // Handle the case where none of the filters are applied (all are null)
    //     else {

    //         $indicators = ResponsiblePerson::where('organisation_id', 1)->pluck('indicator_id');
    //         $query->whereHas('systemReport', function ($query) use ($indicators) {
    //             $query->whereIn('indicator_id', $indicators)
    //                 ->where('organisation_id', 1);
    //         });
    //     }

    //     return $query;
    // }


    public function datasource(): Builder
    {
        // Start building the query for SystemReportData and eager load systemReport
        $query = SystemReportData::query()->with('systemReport')
            ->join('system_reports', function ($join) {
                $join->on('system_reports.id', '=', 'system_report_data.system_report_id');
            })
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



        // If any of the filters are provided, apply them
        if (
            !is_null($this->organisation_id) || !is_null($this->reporting_period) || !is_null($this->financial_year) ||
            !is_null($this->disaggregation) || !is_null($this->indicator)
        ) {

            if (!is_null($this->disaggregation)) {
                // Assuming disaggregations is a column you want to filter by
                $query->where('system_report_data.name', $this->disaggregation);
            }

            // Get the indicators associated with the organisation if organisation_id is set
            $indicators = [];
            if (!is_null($this->organisation_id)) {
                $indicators = ResponsiblePerson::where('organisation_id', $this->organisation_id)->pluck('indicator_id')->toArray();
            }


            // Apply the filters to the query based on the available conditions
            $query->whereHas('systemReport', function ($query) use ($indicators) {
                if (!is_null($this->organisation_id)) {
                    $query->where('organisation_id', $this->organisation_id);
                }
                if (!is_null($indicators)) {
                    if (!is_null($this->indicator)) {

                        $valueToKeep = intval($this->indicator);

                        // Filter the array to keep only the element(s) with the specified value
                        $result = array_filter($indicators, fn($value) => $value === $valueToKeep);

                        $query->whereIn('indicator_id', $result);
                    } else {
                        $query->whereIn('indicator_id', $indicators);
                    }
                }
                if (!is_null($this->financial_year)) {
                    $query->where('financial_year_id', $this->financial_year);
                }
                if (!is_null($this->reporting_period)) {
                    $query->where('reporting_period_id', $this->reporting_period);
                }
            });
        }

        return $query;
    }
    public function relationSearch(): array
    {
        return [
            'systemReport' => [ // relationship on dishes model

                'indicators' => ['indicator_name'],
                'indicators' => ['indicator_no'],
                'organisations' => ['name'],
            ],
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

            Column::make('Disaggregation', 'name')
                ->sortable()->searchable(),
            Column::make('Value', 'value')
                ->sortable(),

            Column::make('Indicator Name', 'indicator_name')
                ->searchable()
                ->sortable(),

            Column::make('Indicator #', 'number', 'indicator_no')
                ->searchable()
                ->sortable(),

            Column::make('Project', 'project'),


            Column::make('Reporting period', 'report_period')->searchable(),
            Column::make('Organisation', 'organisations', 'organisation_name')->sortable()->searchable(),
            Column::make('Project year', 'financial_year')->sortable(),

        ];
    }

    // public function filters(): array
    // {
    //     // dd(Indicator::select('indicator_name')->distinct()->get());
    //     return [

    //         Filter::select('name', 'name')
    //             ->dataSource(IndicatorDisaggregation::select('name')->distinct()->get())
    //             ->optionLabel('name')
    //             ->optionValue('name'),

    //         Filter::select('indicator_name')
    //             ->builder(function ($model) {
    //                 dd($model);
    //             })

    //             // ->dataSource(Indicator::select('indicator_name')->distinct()->get())
    //             // ->optionLabel('indicator_name')
    //             // ->optionValue('indicator_name')

    //             ->filterRelation('systemReport.indicator', 'indicator_name')



    //     ];
    // }

    #[On('filtered-data')]
    public function getData($data)
    {

        //   $this->project = $data['project_id'];
        $this->indicator = $data['indicator'];
        //$this->start_date = $data['start_date'];
        // $this->end_date = $data['end_date'];
        $this->reporting_period = $data['reporting_period'];
        $this->financial_year = $data['financial_year'];
        $this->organisation_id = $data['organisation_id'];
        $this->disaggregation = $data['disaggregation'];
    }

    #[On('reset-filters')]
    public function resetData()
    {
        $this->reset('organisation_id', 'reporting_period', 'financial_year', 'indicator', 'disaggregation', 'project');
        $this->resetPage();
        $this->refresh();
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
