<?php

namespace App\Livewire\targets;

use App\Models\User;
use App\Models\SystemReport;
use Illuminate\Support\Carbon;
use App\Models\SystemReportData;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
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

final class TargetTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('targets')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {


        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return OrganisationTarget::query()->with([
                'submissionTarget',
                'organisation',

                'submissionTarget.financialYear',
                'submissionTarget.Indicator',
                'submissionTarget.Indicator.project',
                'submissionTarget.Indicator.class'
            ])->whereHas('organisation', fn($query) => $query->where('id', $organisation_id));
        }
        return OrganisationTarget::query()->with([
            'submissionTarget',
            'organisation',

            'submissionTarget.financialYear',
            'submissionTarget.Indicator',
            'submissionTarget.Indicator.project',
            'submissionTarget.Indicator.class'
        ]);
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

                $data = $this->calculateRow($model);
                return '<span class="fw-bolder">' . (float) $data . '</span>';
            })


            ->add('progress', function ($model) {



                $data = $this->calculateRow($model);
                $setTarget = $model->submissionTarget->target_value;
                $progress = ($data / $setTarget) * 100;
                $progressColor = match (true) {
                    $progress >= 0 && $progress <= 49 => 'bg-danger',
                    $progress >= 50 && $progress <= 99 => 'bg-warning',
                    $progress === 100 => 'bg-success',
                    default => 'bg-success', // Fallback for unexpected values
                };

                if ($setTarget == 0) {
                    return '<span class="badge bg-warning">Not available!</span>';
                }



                $html = "

                <div class='d-flex justify-content-between align-items-center ' style='min-width:150px'>
<div class='progress progress-sm bg-secondary-subtle w-100 me-3'>
<div class='progress-bar {$progressColor}' role='progressbar' style='width: {$progress}%' aria-valuenow='{$progress}' aria-valuemin='0' aria-valuemax='100'></div>
</div>
<span class='text-muted fw-bold'>{$progress}%</span>
</div>
";


                return $html;
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


            Column::make('Project year', 'financial_year'),


            Column::make('Dissagregation', 'submission_target_name')->bodyAttribute('text-uppercase'),
            Column::make('Standard Target', 'submission_target_value'),


            Column::make('Partner Set Target', 'value')

                ->searchable(),

            Column::make('Achieved Value', 'current_value')->bodyAttribute('fw-bold'),
            Column::make('Percentage Achieved', 'progress')


        ];
    }


    public function calculateRow($model)
    {
        $financialYear = $model->submissionTarget->financialYear->id;
        $organisation = $model->organisation->id;
        $indicatorId = $model->submissionTarget->indicator->id;
        $project = $model->submissionTarget->indicator->project->id;

        $reportIds = SystemReport::where('financial_year_id', $financialYear)
            ->where('project_id', $project)
            ->where('organisation_id', $organisation)->where('indicator_id', $indicatorId)->pluck('id');


        if (count($reportIds) == 0) {
            return '<span class="badge bg-warning-subtle">Not available!</span>';
        }

        $data = SystemReportData::whereIn('system_report_id', $reportIds)->where('name', $model->submissionTarget->target_name)->sum('value');
        return $data;
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
