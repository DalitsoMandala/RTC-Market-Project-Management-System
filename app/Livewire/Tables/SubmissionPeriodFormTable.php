<?php

namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\ResponsiblePerson;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Ramsey\Uuid\Uuid;

final class SubmissionPeriodFormTable extends PowerGridComponent
{
    public string $tableName = 'SubmissionPeriodFormTable';
    use WithExport;

    public array $indicatorIds = [];

    public array $formIds = [];

    public ?int $submissionPeriodRow = null;

    public $currentRoutePrefix;

    public ?int $organisation_id = null;

    /**
     * Initialize the component.
     */
    public function setUp(): array
    {
        $this->organisation_id = auth()
            ->user()
            ->organisation
            ->id;

        return [

               PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /**
     * Get active forms assigned to the current organisation.
     */
    public function datasource(): Builder
    {
        $organisationId = $this->organisation_id
            ?? auth()->user()->organisation->id;

        $myIndicators = ResponsiblePerson::query()
            ->where('organisation_id', $organisationId)
            ->pluck('indicator_id')
            ->unique()
            ->values()
            ->all();

        return Form::query()
            ->with([
                'indicators' => function ($query) {
                    $query->where('indicators.is_active', true);
                },
            ])
            ->where('forms.is_active', true)
            ->whereHas('indicators', function (Builder $query) use ($myIndicators) {
                $query
                    ->whereIn('indicators.id', $myIndicators)
                    ->where('indicators.is_active', true);
            })
            ->select([
                'forms.*',
                DB::raw(
                    'ROW_NUMBER() OVER (ORDER BY forms.id) AS rn'
                ),
            ]);
    }

    /**
     * Get the selected submission period ID.
     */
    private function getCurrentSubmissionPeriodId(): ?int
    {
        return $this->submissionPeriodRow;
    }

    /**
     * PowerGrid fields.
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('type')
            ->add('slug')

            ->add('indicators', function ($model) {
                $indicators = $model->indicators
                    ->pluck('indicator_name')
                    ->toArray();

                return Str::limit(
                    implode(', ', $indicators),
                    50
                );
            })

            ->add('submisssions', function ($model) {
                $organisationId = $this->organisation_id
                    ?? auth()->user()->organisation->id;

                $periodId = $this->getCurrentSubmissionPeriodId();

                if (!$periodId) {
                    return 'No submissions';
                }

                $counts = Submission::query()
                    ->where('form_id', $model->id)

                    ->whereHas('period', function (Builder $query) use ($periodId) {
                        $query->where(
                            'submission_period_new_id',
                            $periodId
                        );
                    })

                    ->whereHas('user.organisation', function (Builder $query) use ($organisationId) {
                        $query->where(
                            'organisations.id',
                            $organisationId
                        );
                    })

                    ->select('batch_type')
                    ->selectRaw('COUNT(*) AS total')
                    ->groupBy('batch_type')
                    ->pluck('total', 'batch_type');

                $batch = $counts->get('batch', 0);

                $manual = $counts->get('manual', 0);

                $aggregate = $counts->get('aggregate', 0);

                if ($model->name === 'REPORT FORM') {
                    return <<<HTML
                        <span class="me-1">
                            Aggregate: <b>{$aggregate}</b>
                        </span>
                    HTML;
                }

                return <<<HTML
                    <span class="me-1">
                        Batch: <b>{$batch}</b>
                    </span>
                    |
                    <span class="me-1">
                        Manual: <b>{$manual}</b>
                    </span>
                HTML;
            })

            ->add('status', function ($model) {
                $periodId = $this->getCurrentSubmissionPeriodId();

                if (!$periodId) {
                    return '<span class="badge text-danger bg-soft-danger">
                        Not Submitted
                    </span>';
                }

                $organisationId = $this->organisation_id
                    ?? auth()->user()->organisation->id;

                $hasSubmission = Submission::query()
                    ->where('form_id', $model->id)

                    ->whereHas('period', function (Builder $query) use ($periodId) {
                        $query->where(
                            'submission_period_new_id',
                            $periodId
                        );
                    })

                    ->whereHas('user.organisation', function (Builder $query) use ($organisationId) {
                        $query->where(
                            'organisations.id',
                            $organisationId
                        );
                    })

                    ->exists();

                if ($hasSubmission) {
                    return '<span class="badge text-success bg-soft-success">
                        Submitted
                    </span>';
                }

                return '<span class="badge text-danger bg-soft-danger">
                    Not Submitted
                </span>';
            })

            ->add('project_id')
            ->add('created_at')
            ->add('updated_at');
    }

    /**
     * Define table columns.
     */
    public function columns(): array
    {
        $user = auth()->user();

        if ($user->hasAnyRole('external')) {
            return [
                    Column::action(''),
                Column::make('#', 'rn'),

                Column::make('Name', 'name'),

                Column::make('Submission Status', 'status'),


            ];
        }

        return [
            Column::action('')
                ->headerAttribute('table-secondary table-bordered'),
            Column::make('#', 'rn')
                ->headerAttribute('table-secondary table-bordered'),

            Column::make('Name', 'name')
                ->headerAttribute('table-secondary table-bordered'),

            Column::make('Submission Status', 'status')
                ->headerAttribute('table-secondary table-bordered'),

            Column::make(
                'Submissions (Per Organisation)',
                'submisssions'
            )->headerAttribute('table-secondary table-bordered'),


        ];
    }

    /**
     * Table filters.
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Edit event.
     */
    #[On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . (int) $rowId . ')');
    }

    /**
     * Define row actions.
     */
    public function actions($row): array
    {
        return [
            Button::add('add-data')
                ->slot('<i class="bx bx-plus"></i>')
                ->id()
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('Add Data')
                ->dispatch('sendData', [
                    'model' => $row,
                ]),

            Button::add('upload')
                ->slot('<i class="bx bx-upload"></i>')
                ->id()
                ->tooltip('Upload Your Data')
                ->class('btn btn-warning my-1 btn-sm custom-tooltip')
                ->dispatch('sendData', [
                    'model' => $row,
                    'upload' => true,
                ]),
        ];
    }

    /**
     * Handle Add Data / Upload Data actions.
     */
    #[On('sendData')]
    public function sendData($model, $upload = false)
    {
        $model = (array) $model;

        $formId = (int) ($model['id'] ?? 0);

        $form = Form::with('project')
            ->findOrFail($formId);

        $submissionPeriodId = $this->getCurrentSubmissionPeriodId();

        if (!$submissionPeriodId) {
            throw new \RuntimeException(
                'The selected submission period could not be found.'
            );
        }

        $route = $this->buildRoute(
            form: $form,
            modelId: $formId,
            submissionPeriodId: $submissionPeriodId,
            upload: (bool) $upload
        );

        return redirect($route);
    }

    /**
     * Build the correct route for the selected form.
     */
    private function buildRoute(
        Form $form,
        int $modelId,
        int $submissionPeriodId,
        bool $upload
    ): string {
        $period = SubmissionPeriod::findOrFail(
            $submissionPeriodId
        );

        $financialYearId = $period->financial_year_id;

        $monthRangePeriodId = $period->reporting_period_month_id;

        $routePrefix = $this->currentRoutePrefix ?? '';

        $formName = $this->slug($form->name);

        $project = $this->slug(
            $form->project->name
        );

        if ($upload) {
            return "{$routePrefix}/forms/{$project}/{$formName}/upload/"
                . "{$modelId}/0/{$financialYearId}/{$monthRangePeriodId}/"
                . "{$submissionPeriodId}/"
                . Uuid::uuid4()->toString();
        }

        if ($form->name === 'REPORT FORM') {
            return "{$routePrefix}/forms/{$project}/aggregate/"
                . "{$modelId}/0/{$financialYearId}/{$monthRangePeriodId}/"
                . "{$submissionPeriodId}";
        }

        return "{$routePrefix}/forms/{$project}/{$formName}/add/"
            . "{$modelId}/0/{$financialYearId}/{$monthRangePeriodId}/"
            . "{$submissionPeriodId}";
    }

    /**
     * Generate a route slug.
     */
    private function slug(string $value): string
    {
        return Str::slug($value);
    }

    /**
     * Handle the separate upload event.
     */
    #[On('sendUploadData')]
    public function sendUploadData($model)
    {
        $model = (array) $model;

        $formId = (int) ($model['form_id'] ?? 0);

        $form = Form::with('project')
            ->findOrFail($formId);

        $periodId = $this->getCurrentSubmissionPeriodId();

        if (!$periodId) {
            throw new \RuntimeException(
                'The selected submission period could not be found.'
            );
        }

        $period = SubmissionPeriod::findOrFail($periodId);

        $routePrefix = $this->currentRoutePrefix ?? '';

        $formName = $this->slug($form->name);

        $project = $this->slug(
            $form->project->name
        );

        $financialYearId = $model['financial_year_id']
            ?? $period->financial_year_id;

        $monthRangePeriodId = $model['reporting_period_month_id']
            ?? $period->reporting_period_month_id;

        $indicatorId = $model['indicator_id'] ?? 0;

        $submissionId = $model['id'] ?? 0;

        $route = "{$routePrefix}/forms/{$project}/{$formName}/upload/"
            . "{$formId}/{$indicatorId}/"
            . "{$financialYearId}/{$monthRangePeriodId}/"
            . "{$submissionId}/"
            . Uuid::uuid4()->toString();

        return redirect($route);
    }

    /**
     * Hide actions based on form type.
     */
    public function actionRules($row): array
    {
        return [

     Rule::button('add-data')
                ->when(
                    fn ($row) => $row->name !== 'REPORT FORM'
                )
                ->hide(),
            Rule::button('upload')
                ->when(
                    fn ($row) => $row->name === 'REPORT FORM'
                )
                ->hide(),
        ];
    }
}
