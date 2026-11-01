<?php

namespace App\Livewire\Tables;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\User;
use App\Traits\DownloadImportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
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

final class AggregateSubmissionTable extends PowerGridComponent
{
    use WithExport;
    use DownloadImportTrait;
    use LivewireAlert;

    public $filter = [];

    public $userId;

    public bool $showFilters = true;

    public $batch;

    public string $sortField = 'submission_id';

    public string $sortDirection = 'desc';

    public string $primaryKey = 'submission_id';

    public string $tableName = 'AggregateSubmissionTable';

    public $routePrefix;

    /**
     * Configure the table.
     */
    public function setUp(): array
    {
        $this->batch = Route::current()?->parameter('batch');

        $this->setLocation('exports');

        return [
            Header::make()
                ->showSearchInput()
                ->showToggleColumns(),

            Footer::make()
                ->showPerPage()
                ->pageName('AggregatePage')
                ->showRecordCount(),
        ];
    }

    /**
     * Main datasource.
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
                'submission_reports',
                'submission_reports.uuid',
                '=',
                'submissions.batch_no'
            )
            ->leftJoin(
                'indicators',
                'indicators.id',
                '=',
                'submission_reports.indicator_id'
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
            ->where('submissions.batch_type', 'aggregate')
            ->select([
                'submissions.*',

                'submissions.id as submission_id',

                'submission_reports.indicator_id as indicator_id',

                'indicators.indicator_no as indicator_no',

                'indicators.indicator_name as indicator_name',

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
     * Relationship search.
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

            'period.indicator' => [
                'indicator_no',
                'indicator_name',
            ],

            'form' => [
                'name',
            ],
        ];
    }

    /**
     * Table filters.
     */
    public function filters(): array
    {
        return [
            Filter::select('indicator')
                ->dataSource(
                    Indicator::query()
                        ->select([
                            'id',
                            'indicator_no',
                            'indicator_name',
                        ])
                        ->orderBy('indicator_no')
                        ->get()
                        ->map(fn ($indicator) => [
                            'indicator' => $indicator->indicator_no
                                . ' - '
                                . $indicator->indicator_name,

                            'id' => $indicator->id,
                        ])
                )
                ->optionLabel('indicator')
                ->optionValue('id')
                ->builder(
                    fn ($query, $value) => $query->where(
                        'submission_reports.indicator_id',
                        $value
                    )
                ),

            Filter::select(
                'status_formatted',
                'submissions.status'
            )
                ->dataSource(
                    Submission::query()
                        ->where('batch_type', 'aggregate')
                        ->select('status')
                        ->distinct()
                        ->get()
                        ->map(fn ($item) => [
                            'status' => $item->status === 'denied'
                                ? 'disapproved'
                                : $item->status,

                            'value' => $item->status,
                        ])
                )
                ->optionLabel('status')
                ->optionValue('value'),

            Filter::select('form_name', 'forms.id')
                ->dataSource(
                    Form::query()
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
                ->dataSource(
                    Organisation::query()
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get()
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'reporting_period',
                'submission_periods_new.reporting_period_month_id'
            )
                ->dataSource(
                    ReportingPeriodMonth::query()
                        ->select([
                            'id',
                            'start_month',
                            'end_month',
                        ])
                        ->orderBy('id')
                        ->get()
                        ->map(fn ($item) => [
                            'name' => $item->start_month
                                . ' - '
                                . $item->end_month,

                            'id' => $item->id,
                        ])
                )
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select(
                'financial_year',
                'submission_periods_new.financial_year_id'
            )
                ->dataSource(
                    FinancialYear::query()
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
     * Table fields.
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()

            ->add('id')

            ->add('batch_no')

            ->add(
                'batch_no_formatted',
                fn ($model) => $this->expandable(
                    $model->batch_no,
                    'Batch number',
                    'batch-' . $model->id
                )
            )

            ->add('user_id')

            ->add(
                'username',
                fn ($model) => $model->username ?? 'N/A'
            )

            ->add('form_id')

            ->add(
                'form_name',
                fn ($model) => $model->form_name ?? 'N/A'
            )

            ->add(
                'organisation_formatted',
                fn ($model) => $model->organisation_formatted ?? 'N/A'
            )

            ->add('status')

            ->add('batch_type')

            ->add('record_filter')

            ->add(
                'status_formatted',
                fn ($model) => $this->statusBadge($model->status)
            )

            ->add('submission_period_new_id')

            ->add(
                'reporting_period',
                fn ($model) => $model->period?->date_established
                    && $model->period?->date_ended
                    ? $model->period->date_established->format('d F Y')
                        . ' - '
                        . $model->period->date_ended->format('d F Y')
                    : 'N/A'
            )

            ->add('comments')

            ->add(
                'comments_truncated',
                fn ($model) => $this->expandable(
                    $model->comments,
                    'Comments',
                    'comments-' . $model->id
                )
            )

            ->add(
                'financial_year',
                fn ($model) => $model->financial_year_number
                    ? 'Year ' . $model->financial_year_number
                    : 'N/A'
            )

            ->add('indicator_id')

            ->add(
                'indicator',
                fn ($model) => $model->indicator_no
                    && $model->indicator_name
                    ? $model->indicator_no . ' - ' . $model->indicator_name
                    : 'N/A'
            )

            ->add(
                'month_range',
                fn ($model) => $model->month_start
                    && $model->month_end
                    ? $model->month_start . ' - ' . $model->month_end
                    : 'N/A'
            )

            ->add('created_at')

            ->add(
                'file_link',
                fn ($model) => $this->fileLink($model->file_link)
            )

            ->add(
                'date_of_submission',
                fn ($model) => $model->created_at?->format(
                    'Y-m-d H:i:s'
                )
            )

            ->add('updated_at');
    }

    /**
     * Expandable text field.
     */

     private function expandableText(
        ?string $text,
        string $tooltip
    ): string {
        if (! $text) {
            return '<span class="text-muted">N/A</span>';
        }

        $safeText = e($text);
        $safeTooltip = e($tooltip);

        return <<<HTML
            <details class="custom-tooltip" title="{$safeTooltip}">
                <summary class="btn btn-sm btn-light border">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </summary>

                <div class="mt-2 text-wrap">
                    {$safeText}
                </div>
            </details>
        HTML;
    }
    private function expandable(
        ?string $content,
        string $label,
        string $id
    ): string {
        if (blank($content)) {
            return '<span class="text-muted">N/A</span>';
        }

        return '
            <details class="border rounded px-2 py-1">
                <summary class="small fw-semibold"
                    style="cursor:pointer">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                    ' . e($label) . '
                </summary>

                <div class="p-2 text-wrap" id="' . e($id) . '">'
                    . nl2br(e($content)) .
                '</div>
            </details>
        ';
    }

    /**
     * Format submission status.
     */
    private function statusBadge(?string $status): string
    {
        $label = $status === 'denied'
            ? 'disapproved'
            : ($status ?? 'N/A');

        $class = match ($status) {
            'approved' => 'bg-success-subtle text-success',
            'pending' => 'bg-warning-subtle text-warning',
            default => 'bg-danger-subtle text-danger',
        };

        return '<span class="badge ' . $class . '">'
            . e($label)
            . '</span>';
    }

    /**
     * File download link.
     */
    private function fileLink(?string $filename): ?string
    {
        if (blank($filename)) {
            return null;
        }

        $url = asset(
            'storage/exports/' . rawurlencode($filename)
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
            Column::make('#', 'rn')
                ->sortable()
                ->hidden(),

            Column::make('File', 'file_link')
                ->hidden(),

            Column::make('Batch no', 'batch_no_formatted')
                ->sortable()
                ->searchable(),

            Column::make(
                'Indicator',
                'indicator',
                'indicators.indicator_name'
            )
                ->searchable()
                ->headerAttribute(styleAttr: 'min-width:350px;')
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
     * Refresh table.
     */
    #[On('refresh')]
    public function refreshData(): void
    {
        $this->dispatch('$refresh');
    }

    /**
     * Table actions.
     */
    public function actions($row): array
    {
        $user = Auth::user();

        $canManage = $user?->hasAnyRole([
            'manager',
            'admin',
        ]) ?? false;

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-analyse"></i>')
                ->id()
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('Review Submission')
                ->can(allowed: $canManage)
                ->dispatch('showAggregate', [
                    'id' => $row->id,
                    'name' => 'view-aggregate-modal',
                ]),

            Button::add('show')
                ->slot('<i class="fas fa-eye"></i>')
                ->id()
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('View Data')
                ->dispatch('showDataAggregate', [
                    'id' => $row->id,
                    'name' => 'view-data-agg-modal',
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red btn-sm my-1 custom-tooltip')
                ->tooltip('Delete Data')
                ->can(allowed: $canManage)
                ->dispatch('deleteAggregate', [
                    'id' => $row->id,
                    'name' => 'delete-aggregate-modal',
                ]),
        ];
    }

    /**
     * Button and row rules.
     */
    public function actionRules(): array
    {
        $user = Auth::user();

        $canManage = $user?->hasAnyRole([
            'manager',
            'admin',
        ]) ?? false;

        return [
            Rule::button('edit')
                ->when(fn ($row) => in_array(
                    $row->status,
                    ['denied', 'disapproved'],
                    true
                ))
                ->disable(),

            Rule::button('delete')
                ->when(fn ($row) => $row->status === 'pending')
                ->disable(),

            Rule::button('delete')
                ->when(fn () => ! $canManage)
                ->disable(),

            Rule::rows()
                ->when(fn ($row) =>
                    $this->batch !== null &&
                    $row->batch_no === $this->batch
                )
                ->setAttribute('class', 'table-secondary'),
        ];
    }
}
