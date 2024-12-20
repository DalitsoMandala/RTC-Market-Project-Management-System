<?php

namespace App\Livewire\targets;

use App\Models\User;
use App\Models\SystemReport;
use Illuminate\Support\Carbon;
use App\Models\SystemReportData;
use App\Models\OrganisationTarget;
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


                $financialYear = $model->submissionTarget->financialYear->id;
                $organisation = $model->organisation->id;
                $indicatorId = $model->submissionTarget->indicator->id;
                $project = $model->submissionTarget->indicator->project->id;

                $reportIds = SystemReport::where('financial_year_id', $financialYear)
                    ->where('project_id', $project)
                    ->where('organisation_id', $organisation)->where('indicator_id', $indicatorId)->pluck('id');


                if (count($reportIds) == 0) {
                    return '<span class="badge bg-warning">Not available!</span>';
                }

                $data = SystemReportData::whereIn('system_report_id', $reportIds)->where('name', $model->submissionTarget->target_name)->sum('value');


                if ($data) {
                    $target_value = ($model->submissionTarget->target_value + 0);



                    return '<span class="badge bg-secondary fs-6">' . (float) $data . '</span>';

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


            Column::make('Project year', 'financial_year'),


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
