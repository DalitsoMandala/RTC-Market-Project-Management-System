<?php

namespace App\Livewire\tables;

use App\Helpers\TruncateText;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\User;
use App\Traits\DownloadImportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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

final class ManualSubmissionTable extends PowerGridComponent
{
    use LivewireAlert;
    use DownloadImportTrait;
    use WithExport;

    public $filter;

    public $userId;

    public bool $showFilters = true;

    public $batch;

    public string $sortField = 'submission_id';

    public string $primaryKey = 'submission_id';

    public string $sortDirection = 'desc';

    public $routePrefix;

    /**
     * Configure the PowerGrid table.
     */
    public function setUp(): array
    {
        $parameters = Route::current()?->parameters() ?? [];

        $this->batch = $parameters['manual'] ?? null;

        return [
            Header::make()
                ->showSearchInput()
                ->showToggleColumns(),

            Footer::make()
                ->showPerPage()
                ->pageName('manualSubmissionPage')
                ->showRecordCount(),
        ];
    }

    /**
     * Build the table query.
     */
    public function datasource(): Builder
    {
        $user = auth()->user();

        $query = Submission::query()
            ->where('submissions.batch_type', 'manual')
            ->leftJoin(
                'forms',
                'forms.id',
                '=',
                'submissions.form_id'
            )
            ->leftJoin(
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
            ->with([

                'period.reportingMonths',
                'period.financialYears',
                'form',
                'user' => fn($query) => $query->withTrashed(),
                'user.organisation',
            ])
            ->select([
                'submissions.*',
                'submissions.id as submission_id',
                'users.name as username',
                'forms.name as form_name',
                'organisations.name as organisation_name',
                'organisations.id as organisation_id',
                'submission_periods_new.reporting_period_month_id',
                'submission_periods_new.financial_year_id',
                DB::raw(
                    'ROW_NUMBER() OVER (ORDER BY submissions.id DESC) AS rn'
                ),
            ]);

        /*
         * External users can only see their own submissions.
         */
        if ($user?->hasRole('external')) {
            $query->where('submissions.user_id', $user->id);
        }

        return $query;
    }

    /**
     * Configure table filters.
     */
    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'submissions.status')
                ->dataSource(function () {
                    return Submission::query()
                        ->where('batch_type', 'manual')
                        ->select('status')
                        ->distinct()
                        ->get()
                        ->map(fn($submission) => [
                            'status' => $this->formatStatus(
                                $submission->status
                            ),
                            'value' => $submission->status,
                        ]);
                })
                ->optionLabel('status')
                ->optionValue('value'),

            Filter::select('form_name', 'forms.id')
                ->dataSource(
                    fn() => Form::query()
                        ->select(['name', 'id'])
                        ->distinct()
                        ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'organisation_formatted',
                'organisations.id'
            )
                ->dataSource(
                    fn() => Organisation::query()
                        ->select(['name', 'id'])
                        ->distinct()
                        ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'reporting_period',
                'submission_periods_new.reporting_period_month_id'
            )
                ->dataSource(
                    fn() => ReportingPeriodMonth::query()
                        ->select(['start_month', 'end_month', 'id'])
                        ->distinct()
                        ->get()
                        ->map(fn($period) => [
                            'name' => "{$period->start_month} - {$period->end_month}",
                            'id' => $period->id,
                        ])
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'financial_year',
                'submission_periods_new.financial_year_id'
            )
                ->dataSource(
                    fn() => FinancialYear::query()
                        ->select(['id', 'number'])
                        ->distinct()
                        ->get()
                        ->map(fn($year) => [
                            'number' => 'Year ' . $year->number,
                            'id' => $year->id,
                        ])
                )
                ->optionLabel('number')
                ->optionValue('id'),
        ];
    }

    /**
     * Configure relationship searching.
     */
    public function relationSearch(): array
    {
        return [
            'period.indicator' => [
                'indicator_name',
            ],

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
     * Define the fields displayed or used by the table.
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rn')
            ->add('batch_no')
            ->add(
                'batch_no_formatted',
                fn($model) => $this->expandableText(
                    $model->batch_no,
                    'batch-' . $model->id,
                    'Show batch number'
                )
            )
            ->add('user_id')
            ->add('username', fn($model) => $model->username ?? $model->user?->name)
            ->add('form_id')
            ->add('form_name', fn($model) => $model->form_name ?? $model->form?->name)
            ->add('organisation_formatted', fn($model) => $model->organisation_name ?? $model->user?->organisation?->name)
            ->add('status')
            ->add('status_formatted', fn($model) => $this->statusBadge($model->status))
            ->add('batch_type')
            ->add('reporting_period_month_id')
            ->add('reporting_period', fn($model) => $this->formatReportingPeriod($model))
            ->add('month_range', fn($model) => $this->formatMonthRange($model))
            ->add('financial_year', fn($model) => $model->period?->financialYears?->number
                ? 'Year ' . $model->period->financialYears->number
                : 'N/A')
            ->add('comments')
            ->add(
                'comments_truncated',
                fn($model) => $this->expandableText(
                    $model->comments,
                    'comments-' . $model->id,
                    'Show comments'
                )
            )
            ->add('description')
            ->add('file_link', fn($model) => $this->fileLink($model->file_link))
            ->add(
                'date_of_submission',
                fn($model) => $model->created_at
                    ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s')
                    : null
            )
            ->add('created_at')
            ->add('updated_at');
    }

    /**
     * Define table columns.
     */
    public function columns(): array
    {
        return [
            Column::make('#', 'rn')
                ->sortable()
                ->hidden(),

            Column::make('File', 'file_link')
                ->hidden(),

            Column::make(
                'Batch no',
                'batch_no_formatted',
                'submissions.batch_no'
            )
                ->searchable(),

            Column::make('Form name', 'form_name')
                ->searchable()->headerAttribute(styleAttr: 'min-width:350px;')
                ->bodyAttribute(styleAttr: 'white-space:wrap'),

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
                ->searchable()
                ->hidden(),

            Column::make('Comments', 'comments_truncated')
                ->hidden(),

            Column::make('Description', 'description')
                ->hidden(),

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
     * Refresh the table after an event.
     */
    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    /**
     * Define row actions.
     */
    public function actions($row): array
    {
        $canManage = auth()->user()?->hasAnyRole([
            'manager',
            'admin',
        ]) ?? false;

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-analyse"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->can($canManage)
                ->tooltip('Review Submission')
                ->dispatch('showModal', [
                    'id' => $row->id,
                    'name' => 'view-submission-modal',
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can($canManage)
                ->tooltip('Delete Data')
                ->dispatch('deleteBatch', [
                    'id' => $row->id,
                    'name' => 'delete-batch-modal',
                ]),

            Button::add('view')
                ->slot('<i class="bx bx-link"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->tooltip('View Data')
                ->dispatch('view-data-manual', [
                    'batch_no' => $row->batch_no,
                    'form_name' => $row->form_name,
                ]),
        ];
    }

    /**
     * Redirect to the manual submission data page.
     */
    #[On('view-data-manual')]
    public function viewData($batch_no, $form_name)
    {
        $routePrefix = $this->routePrefix;
        $project = Project::query()
            ->where('name', 'RTC MARKET')
            ->value('name');

        if (! $routePrefix || ! $project || ! $form_name || ! $batch_no) {
            $this->alert('error', 'Missing parameters to view data', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }

        $formSlug = strtolower(str_replace(' ', '-', $form_name));
        $projectSlug = strtolower(str_replace(' ', '-', $project));

        $route = "{$routePrefix}/forms/{$projectSlug}/{$formSlug}/view/{$batch_no}";

        return $this->redirect($route);
    }

    /**
     * Configure conditional button and row rules.
     */
    public function actionRules(): array
    {
        $user = auth()->user();

        $canManage = $user?->hasAnyRole([
            'manager',
            'admin',
        ]) ?? false;

        return [
            Rule::button('edit')
                ->when(
                    fn($row) => in_array(
                        $row->status,
                        ['denied', 'disapproved'],
                        true
                    )
                )
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => $row->status === 'pending')
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => ! $canManage)
                ->disable(),

            Rule::rows()
                ->when(
                    fn($row) => (string) $row->batch_no === (string) $this->batch
                )
                ->setAttribute('class', 'table-secondary'),

            Rule::button('bulk-download')
                ->when(fn() => true)
                ->disable(),
        ];
    }

    /**
     * Convert denied status to the label used in the table.
     */
    private function formatStatus(?string $status): string
    {
        return $status === 'denied'
            ? 'disapproved'
            : (string) $status;
    }

    /**
     * Render the status badge.
     */
    private function statusBadge(?string $status): string
    {
        $label = $this->formatStatus($status);

        $class = match ($label) {
            'approved' => 'bg-success-subtle text-success',
            'pending' => 'bg-warning-subtle text-warning',
            default => 'bg-danger-subtle text-danger',
        };

        return '<span class="badge ' . $class . '">' . e($label) . '</span>';
    }

    /**
     * Format the reporting-period date range.
     */
    private function formatReportingPeriod($model): string
    {
        $period = $model->period;

        if (! $period?->date_established || ! $period?->date_ended) {
            return 'N/A';
        }

        return Carbon::parse($period->date_established)->format('d F Y')
            . ' - '
            . Carbon::parse($period->date_ended)->format('d F Y');
    }

    /**
     * Format the reporting month range.
     */
    private function formatMonthRange($model): string
    {
        $months = $model->period?->reportingMonths;

        if (! $months) {
            return 'N/A';
        }

        return $months->start_month . ' - ' . $months->end_month;
    }

    /**
     * Render expandable text without inserting unescaped user content.
     */
    private function expandableText(
        ?string $text,
        string $id,
        string $tooltip
    ): string {
        if (! $text) {
            return '<span class="text-muted">N/A</span>';
        }

        $safeId = e($id);
        $safeText = e($text);
        $safeTooltip = e($tooltip);

        return <<<HTML
            <details class="custom-tooltip" title="{$safeTooltip}">
                <summary class="btn btn-sm btn-light border">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </summary>
                <div class="mt-2 text-wrap">{$safeText}</div>
            </details>
        HTML;
    }

    /**
     * Render a downloadable file link.
     */
    private function fileLink(?string $fileName): ?string
    {
        if (! $fileName) {
            return null;
        }

        $safeFileName = e($fileName);
        $url = e(asset('storage/imports/' . $fileName));

        return <<<HTML
            <a
                class="text-success custom-tooltip"
                title="Download file"
                download="{$safeFileName}"
                href="{$url}"
            >
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <div class="px-2 py-1 rounded-1 bg-success-subtle">
                            <i class="fas fa-file-excel text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 fw-bolder">
                        {$safeFileName}
                    </div>
                </div>
            </a>
        HTML;
    }
}
