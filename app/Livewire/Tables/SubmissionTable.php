<?php

namespace App\Livewire\Tables;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use App\Services\ProjectService;
use App\Traits\DownloadImportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SubmissionTable extends PowerGridComponent
{
    use LivewireAlert;
    use DownloadImportTrait;
    use WithExport;

    public $filter = [];

    public $userId;

    public bool $showFilters = true;

    public $batch;

    public string $sortField = 'submission_id';

    public string $primaryKey = 'submission_id';

    public string $sortDirection = 'desc';

    public string $tableName = 'SubmissionsTable';

    public $routePrefix;

    /**
     * Component configuration.
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        $this->batch = Route::current()?->parameter('batch');

        return [
            Header::make()
                ->showSearchInput()
                ->showToggleColumns(),

            Footer::make()
                ->showPerPage()
                ->pageName('submissionPage')
                ->showRecordCount(),
        ];
    }

    /**
     * Main datasource.
     *
     * Joins are used for searchable and filterable columns.
     * Eager loading is retained for relationships used elsewhere.
     */
    public function datasource(): Builder
    {
        $user = Auth::user();

        $query = Submission::query()
            ->join(
                'forms',
                'forms.id',
                '=',
                'submissions.form_id'
            )
            ->join(
                'submission_periods_new',
                'submission_periods_new.id',
                '=',
                'submissions.submission_period_new_id'
            )
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'submissions.user_id'
            )
            ->leftJoin(
                'organisations',
                'organisations.id',
                '=',
                'users.organisation_id'
            )
            ->leftJoin(
                'reporting_period_months',
                'reporting_period_months.id',
                '=',
                'submission_periods_new.reporting_period_month_id'
            )
            ->leftJoin(
                'financial_years',
                'financial_years.id',
                '=',
                'submission_periods_new.financial_year_id'
            )
            ->with([
                'user' => fn ($q) => $q->withTrashed(),
                'user.organisation',
                'period.reportingMonths',
                'period.financialYears',
                'form',
            ])
            ->where('submissions.batch_type', 'batch')
            ->select([
                'submissions.*',

                'submissions.id as submission_id',

                'users.name as username',

                'forms.name as form_name',

                'organisations.name as organisation_formatted',

                'organisations.id as organisation_id',

                'submission_periods_new.id as submission_period_new_id',

                'submission_periods_new.reporting_period_month_id as reporting_period_month_id',

                'submission_periods_new.financial_year_id as financial_year_id',

                'reporting_period_months.start_month as month_start',

                'reporting_period_months.end_month as month_end',

                'financial_years.number as financial_year_number',
            ]);

        // External users can only view their own submissions.
        if ($user?->hasAnyRole('external')) {
            $query->where('submissions.user_id', $user->id);
        }

        return $query;
    }

    /**
     * Table filters.
     */
    public function filters(): array
    {
        return [
            Filter::select(
                'status_formatted',
                'submissions.status'
            )
                ->dataSource(function () {
                    return Submission::query()
                        ->select('status')
                        ->where('batch_type', 'batch')
                        ->distinct()
                        ->get()
                        ->map(fn ($item) => [
                            'status' => $item->status === 'denied'
                                ? 'disapproved'
                                : $item->status,
                            'value' => $item->status,
                        ]);
                })
                ->optionLabel('status')
                ->optionValue('value'),

            Filter::select('form_name', 'forms.id')
                ->dataSource(fn () => Form::query()
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'organisation_formatted',
                'organisations.id'
            )
                ->dataSource(fn () => Organisation::query()
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'month_range',
                'submission_periods_new.reporting_period_month_id'
            )
                ->dataSource(fn () => ReportingPeriodMonth::query()
                    ->select(['id', 'start_month', 'end_month'])
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($item) => [
                        'name' => "{$item->start_month} - {$item->end_month}",
                        'id' => $item->id,
                    ])
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'financial_year',
                'submission_periods_new.financial_year_id'
            )
                ->dataSource(fn () => FinancialYear::query()
                    ->select(['id', 'number'])
                    ->orderBy('number')
                    ->get()
                    ->map(fn ($item) => [
                        'number' => 'Year ' . $item->number,
                        'id' => $item->id,
                    ])
                )
                ->optionLabel('number')
                ->optionValue('id'),
        ];
    }

    /**
     * Relationships available for PowerGrid searching.
     */
    public function relationSearch(): array
    {
        return [
            'user.organisation' => [
                'name',
            ],

            'user' => [
                'name',
            ],

            'period.reportingMonths' => [
                'start_month',
                'end_month',
            ],

            'period.financialYears' => [
                'number',
            ],

            'form' => [
                'name',
            ],
        ];
    }

    /**
     * Table fields.
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()

            ->add('id')

            ->add('batch_no')

            // Expandable batch number.
            ->add('batch_no_formatted', fn ($model) => $this->expandable(
                $model->batch_no,
                'Batch number',
                'batch-' . $model->id
            ))

            ->add('user_id')

            ->add('username', fn ($model) => $model->username ?? 'N/A')

            ->add('form_id')

            // Uses the joined form name instead of Form::find().
            ->add('form_name', fn ($model) => $model->form_name ?? 'N/A')

            ->add('organisation_formatted', fn ($model) =>
                $model->organisation_formatted ?? 'N/A'
            )

            ->add('status')

            ->add('batch_type')

            ->add('record_filter')

            ->add('status_formatted', function ($model) {
                $status = $model->status;

                $label = $status === 'denied'
                    ? 'disapproved'
                    : $status;

                $class = match ($status) {
                    'approved' => 'bg-success-subtle text-success',
                    'pending' => 'bg-warning-subtle text-warning',
                    default => 'bg-danger-subtle text-danger',
                };

                return '<span class="badge ' . $class . '">'
                    . e($label)
                    . '</span>';
            })

            // Reporting month from the joined reporting_period_months table.
            ->add('month_range', fn ($model) =>
                $model->month_start && $model->month_end
                    ? "{$model->month_start} - {$model->month_end}"
                    : 'N/A'
            )

            // Financial year from the joined financial_years table.
            ->add('financial_year', fn ($model) =>
                $model->financial_year_number
                    ? 'Year ' . $model->financial_year_number
                    : 'N/A'
            )

            ->add('comments')

            ->add('comments_truncated', fn ($model) =>
                $this->expandable(
                    $model->comments,
                    'Comments',
                    'comments-' . $model->id
                )
            )

            ->add('description')

            ->add('created_at')

            ->add('updated_at')

            ->add('date_of_submission', fn ($model) =>
                $model->created_at?->format('Y-m-d H:i:s')
            )

            ->add('file_link', fn ($model) =>
                $this->fileLink($model->file_link)
            );
    }

    /**
     * Generate expandable content without requiring Bootstrap JavaScript.
     */
    private function expandable(
        ?string $content,
        string $label,
        string $id
    ): string {
        if (blank($content)) {
            return '<span class="text-muted">N/A</span>';
        }

        return '
            <details class="custom-tooltip border rounded px-2 py-1" title="View ' . e($label) . '">
                <summary class="small fw-semibold"
                    style="cursor:pointer" >
                    <i class="bx bx-dots-horizontal-rounded"></i>
                    <span class="visually-hidden">'
                        . e($label) .
                    '</span>
                </summary>

                <div class="p-2 text-wrap" id="' . e($id) . '">'
                    . nl2br(e($content)) .
                '</div>
            </details>
        ';
    }

    /**
     * Generate file download link.
     */
    private function fileLink(?string $filename): ?string
    {
        if (blank($filename)) {
            return null;
        }

        $url = asset(
            'storage/imports/' . rawurlencode($filename)
        );

        return '
            <a class="text-success custom-tooltip"
                title="Download file"
                download="' . e($filename) . '"
                href="' . e($url) . '">

                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <div class="px-2 py-1 rounded-1 bg-success-subtle">
                            <i class="fas fa-file-excel text-success"></i>
                        </div>
                    </div>

                    <div class="flex-grow-1 fw-bolder">'
                        . e($filename) .
                    '</div>
                </div>
            </a>
        ';
    }

    /**
     * Table columns.
     */
    public function columns(): array
    {
        return [
            Column::make('Batch no', 'batch_no_formatted', 'submissions.batch_no')
                ->searchable(),

            Column::make('File', 'file_link'),

            Column::make('Form name', 'form_name')
                ->searchable(),

            Column::make('SUBMITTED BY', 'username')
                ->searchable(),

            Column::make('Organisation', 'organisation_formatted')
                ->searchable(),

            Column::make('SUBMISSION PERIOD', 'month_range')
                ->searchable(),

            Column::make('Project Year', 'financial_year')
                ->searchable(),

            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),

            Column::make('Comments', 'comments_truncated'),

            Column::make('Description', 'description')
                ->searchable(),

            Column::make(
                'Date of submission',
                'date_of_submission',
                'submissions.created_at'
            )
                ->sortable(),

            Column::action('Action'),
        ];
    }

    /**
     * Refresh table when requested.
     */
    #[On('refresh')]
    public function refreshData(): void
    {
        $this->dispatch('$refresh');
    }

    /**
     * Table action buttons.
     */
    public function actions($row): array
    {
        $user = Auth::user();

        $canManage = $user?->hasAnyRole(['manager', 'admin']) ?? false;

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-analyse"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->can(allowed: $canManage)
                ->tooltip('Review Submission')
                ->dispatch('showModal', [
                    'id' => $row->id,
                    'name' => 'view-submission-modal',
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can(allowed: $canManage)
                ->tooltip('Delete Data')
                ->dispatch('deleteBatch', [
                    'id' => $row->id,
                    'name' => 'delete-batch-modal',
                ]),

            Button::add('view-data')
                ->slot('<i class="bx bx-link"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->tooltip('View Data')
                ->dispatch('view-data-submission', [
                    'batch_no' => $row->batch_no,
                    'form_name' => $row->form_name,
                ]),
        ];
    }

    /**
     * Navigate to the submitted batch data.
     */
    #[On('view-data-submission')]
    public function viewData($batch_no, $form_name): void
    {


        if (
            blank($this->routePrefix) ||
            blank(ProjectService::app_project_name()) ||
            blank($form_name) ||
            blank($batch_no)
        ) {
            $this->alert('error', 'Missing parameters to view data', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }



        $formSlug = str($form_name)->slug();
        $projectId= ProjectService::app_project_id();

        $route = route($this->routePrefix.'-'.$formSlug.'-view', ['batch' => $batch_no,'project'=> $projectId]);

        $this->redirect($route);
    }

    /**
     * Conditional row and button rules.
     */
    public function actionRules(): array
    {
        $user = Auth::user();

        $canManage = $user?->hasAnyRole(['manager', 'admin']) ?? false;

        return [
            // Do not allow editing disapproved submissions.
            Rule::button('edit')
                ->when(fn ($row) =>
                    in_array($row->status, ['denied', 'disapproved'], true)
                )
                ->disable(),

            // Do not allow deletion of pending submissions.
            Rule::button('delete')
                ->when(fn ($row) => $row->status === 'pending')
                ->disable(),

            // Only managers and administrators can delete.
            Rule::button('delete')
                ->when(fn () => ! $canManage)
                ->disable(),

            Rule::button('view-data')
              ->when(fn ($row) =>
                    in_array($row->status, ['denied', 'disapproved'], true)
                )
                ->disable(),

            // Highlight the selected batch.
            Rule::rows()
                ->when(fn ($row) =>
                    $this->batch !== null &&
                    $row->batch_no === $this->batch
                )
                ->setAttribute('class', 'table-secondary'),

            // Disable unsupported bulk download action.
            Rule::button('bulk-download')
                ->when(fn () => true)
                ->disable(),
        ];
    }
}
