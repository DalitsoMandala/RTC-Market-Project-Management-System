<?php

namespace App\Livewire\tables;

use App\Models\MarketDataSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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

final class MarketDataSubmissionTable extends PowerGridComponent
{
    use WithExport;
    use LivewireAlert;

    public $filter;

    public $userId;

    public bool $showFilters = true;

    public $batch;

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public $routePrefix;

    /**
     * Configure the table.
     */
    public function setUp(): array
    {
        $parameters = Route::current()?->parameters() ?? [];

        $this->batch = $parameters['batch'] ?? null;

        return [
            Header::make()
                ->showSearchInput()
                ->showToggleColumns(),

            Footer::make()
                ->showPerPage()
                ->pageName('marketDataPage')
                ->showRecordCount(),
        ];
    }

    /**
     * Build the table query.
     */
    public function datasource(): Builder
    {
        $user = auth()->user();

        $query = MarketDataSubmission::query()
            ->with([
                'user' => fn ($query) => $query->withTrashed(),
                'user.organisation',
            ])
            ->select([
                'marketing_data_submissions.*',
            ]);

        if ($user?->hasRole('external')) {
            $query->where(
                'marketing_data_submissions.submitted_user_id',
                $user->id
            );
        }

        return $query->selectRaw(
            'ROW_NUMBER() OVER (ORDER BY marketing_data_submissions.id) AS rn'
        );
    }

    /**
     * Configure table filters.
     */
    public function filters(): array
    {
        return [
            Filter::select(
                'status_formatted',
                'marketing_data_submissions.status'
            )
                ->dataSource(
                    fn () => MarketDataSubmission::query()
                        ->select('status')
                        ->distinct()
                        ->get()
                        ->map(fn ($submission) => [
                            'status' => $this->formatStatus(
                                $submission->status
                            ),
                            'value' => $submission->status,
                        ])
                )
                ->optionLabel('status')
                ->optionValue('value'),
        ];
    }

    /**
     * Configure relationship searching.
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
        ];
    }

    /**
     * Define table fields.
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rn')
            ->add('batch_no')
            ->add(
                'batch_no_formatted',
                fn ($model) => $this->expandableText(
                    $model->batch_no,
                    'Show batch number'
                )
            )
            ->add('submitted_user_id')
            ->add(
                'username',
                fn ($model) => $model->user?->name ?? 'N/A'
            )
            ->add('status')
            ->add(
                'status_formatted',
                fn ($model) => $this->statusBadge($model->status)
            )
            ->add('comments')
            ->add(
                'comments_truncated',
                fn ($model) => $this->expandableText(
                    $model->comments,
                    'Show comments'
                )
            )
            ->add(
                'file_link',
                fn ($model) => $this->fileLink($model->file_link)
            )
            ->add(
                'date_of_submission',
                fn ($model) => $model->created_at
                    ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s')
                    : null
            )
            ->add('created_at')
            ->add('updated_at');
    }

    /**
     * Define visible table columns.
     */
    public function columns(): array
    {
        return [
            Column::make(
                'Batch no',
                'batch_no_formatted',
                'marketing_data_submissions.batch_no'
            )
                ->searchable(),

            Column::make('#', 'rn')
                ->sortable()
                ->hidden(),

            Column::make('File', 'file_link'),

            Column::make('SUBMITTED BY', 'username')
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
                'marketing_data_submissions.created_at'
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
     * Configure row actions.
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
                ->dispatch('showMarket', [
                    'id' => $row->id,
                    'name' => 'view-market-modal',
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can($canManage)
                ->tooltip('Delete Data')
                ->dispatch('deleteMarketBatch', [
                    'id' => $row->id,
                    'name' => 'delete-market-modal',
                ]),

            Button::add('view-data')
                ->slot('<i class="bx bx-link"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->tooltip('View Data')
                ->dispatch('view-data-market', [
                    'batch_no' => $row->batch_no,
                ]),
        ];
    }

    /**
     * Redirect to the market data page.
     */
    #[On('view-data-market')]
    public function viewData($batch_no)
    {
        $routePrefix = $this->routePrefix;

        if (! $routePrefix || ! $batch_no) {
            $this->alert('error', 'Missing parameters to view data', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }

        $route = "{$routePrefix}/marketing/manage-data/{$batch_no}";

        return $this->redirect($route);
    }

    /**
     * Configure action and row rules.
     */
    public function actionRules(): array
    {
        $canManage = auth()->user()?->hasAnyRole([
            'manager',
            'admin',
        ]) ?? false;

        return [
            Rule::button('edit')
                ->when(
                    fn ($row) => in_array(
                        $row->status,
                        ['denied', 'disapproved'],
                        true
                    )
                )
                ->disable(),

            Rule::button('delete')
                ->when(fn ($row) => $row->status === 'pending')
                ->disable(),

            Rule::button('delete')
                ->when(fn ($row) => ! $canManage)
                ->disable(),

            Rule::rows()
                ->when(
                    fn ($row) => (string) $row->batch_no === (string) $this->batch
                )
                ->setAttribute('class', 'table-secondary'),
        ];
    }

    /**
     * Format the status label.
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
     * Render expandable text.
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

    /**
     * Render the downloadable file link.
     */
    private function fileLink(?string $fileName): ?string
    {
        if (! $fileName) {
            return null;
        }

        $safeFileName = e($fileName);
        $url = e(asset(
            'storage/imports/' . rawurlencode($fileName)
        ));

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
