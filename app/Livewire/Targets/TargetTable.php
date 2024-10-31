<?php

namespace App\Livewire\targets;

use App\Models\OrganisationTarget;
use App\Models\SystemReport;
use App\Models\SystemReportData;
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

final class TargetTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()
                ->showPerPage(5)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {



        return OrganisationTarget::query()->with(
            'submissionTarget',
            'organisation',
            'submissionTarget.reportPeriodMonth',
            'submissionTarget.financialYear',
            'submissionTarget.Indicator',
            'submissionTarget.Indicator.project',
            'submissionTarget.Indicator.class'
        );
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('organisation_id')
            ->add('organisation', function ($model) {
                return $model->organisation->name;
            })
            ->add('indicator', function ($model) {
                return $model->submissionTarget->indicator->indicator_name;
            })
            ->add('report_period_month', function ($model) {

                return $model->submissionTarget->reportPeriodMonth->start_month . '-' . $model->submissionTarget->reportPeriodMonth->end_month;
            })
            ->add('financial_year', function ($model) {
                return $model->submissionTarget->financialYear->number;
            })
            ->add('submission_target_id')
            ->add('submission_target_name', function ($model) {
                return $model->submissionTarget->target_name;
            })
            ->add('submission_target_value', function ($model) {
                return $model->submissionTarget->target_value;
            })
            ->add('current_value', function ($model) {

                $reportingPeriod = $model->submissionTarget->reportPeriodMonth->id;
                $financialYear = $model->submissionTarget->financialYear->id;
                $organisation = $model->organisation->id;
                $indicatorId = $model->submissionTarget->indicator->id;
                $project = $model->submissionTarget->indicator->project->id;

                $reportId = SystemReport::where('financial_year_id', $financialYear)
                    ->where('project_id', $project)
                    ->where('organisation_id', $organisation)->where('reporting_period_id', $reportingPeriod)->where('indicator_id', $indicatorId)->first();


                if (!$reportId) {
                    return 'N/A';
                }

                $data = SystemReportData::where('system_report_id', $reportId->id)->where('name', $model->submissionTarget->target_name)->first();
                if ($data) {
                    return $data->value;
                }

                return 0;

            })



            ->add('value')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),

            Column::make('Organisation', 'organisation'),
            Column::make('Indicator', 'indicator'),

            Column::make('Reporting period', 'report_period_month')
            ,
            Column::make('Financial year', 'financial_year'),


            Column::make('Submission Target Name', 'submission_target_name')->bodyAttribute('fw-bold'),
            Column::make('Submission Target Value', 'submission_target_value'),


            Column::make('Organisation Target Value', 'value')

                ->searchable(),

            Column::make('Final Value', 'current_value')->bodyAttribute('fw-bold'),


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
