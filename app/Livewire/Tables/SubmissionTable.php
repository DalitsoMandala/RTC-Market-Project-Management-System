<?php

namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\Organisation;
use App\Helpers\TruncateText;
use Illuminate\Support\Carbon;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SubmissionTable extends PowerGridComponent
{

    //Batch Submission Table
    use WithExport;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public $batch;
    public $row = 1;
    public string $sortField = 'id';

    public string $sortDirection = 'desc';
    public function setUp(): array
    {
        // $this->showCheckBox();
        $route = Route::current();
        $parameters = $route->parameters();
        $collection = collect($parameters);
        if ($collection->has('batch')) {
            $this->batch = $collection->get('batch');
        }
        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = Submission::query()->with(['period.indicator', 'user.organisation', 'user',  'period.reportingMonths', 'form', 'financial_year'])
            ->where('batch_type', 'batch');

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return $query->where('user_id', $user->id)->select([
                '*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
        }

        return $query->select([
            '*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'status')
                ->dataSource(function () {
                    $submission = Submission::select(['status'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('status')
                ->optionValue('status'),


            //     Filter::inputText('batch_no_formatted', 'batch_no'),
            //      Filter::inputText('indicator')->filterRelation('period.indicator', 'indicator_name'),
            //      Filter::inputText('organisation_formatted')->filterRelation('user.organisation', 'name'),
        ];
    }
    public function relationSearch(): array
    {
        return [
            'period.indicator' => [ // relationship on dishes model
                'indicator_name', // column enabled to search

            ],
            'user.organisation' => [
                'name',
            ],
            'user' => [
                'name'
            ],

            'period.reportingMonths' => [
                'start_month',
                'end_month',
            ],
            'form' => [
                'name',
            ],

            'financial_year' => [
                'number'
            ]


        ];
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('row_num', function () {
                return $this->row++;
            })
            ->add('batch_no')
            ->add('batch_no_formatted', function ($model) {

                return $model->batch_no;
            })
            ->add('user_id')
            ->add('username', function ($model) {
                return User::find($model->user_id)->name;
            })
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);


                return $form->name;
            })
            ->add('organisation')
            ->add('organisation_formatted', function ($model) {

                $user = User::find($model->user_id);

                return $user->organisation->name;
            })
            ->add('status')
            ->add('batch_type')
            ->add('record_filter', function ($model) {})
            ->add('status_formatted', function ($model) {

                if ($model->status === 'approved') {
                    return '<span class="badge bg-success-subtle text-success">' . $model->status . '</span>';
                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning-subtle text-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-danger-subtle text-danger">' . $model->status . '</span>';
                }
            })

            ->add('period_id')
            ->add('reporting_period', function ($model) {

                $period = SubmissionPeriod::find($model->period_id);
                if ($period) {
                    return Carbon::parse($period->date_established)->format('d F Y') . '-' . Carbon::parse($period->date_ended)->format('d F Y');
                } else {
                    return 'N/A';
                }
            })
            ->add('comments')
            ->add('comments_truncated', function ($model) {
                $text = $model->comments;
                $trunc = new TruncateText($text, 30);

                return $trunc->truncate();
            })
            ->add('financial_year', function ($model) {

                $model = SubmissionPeriod::find($model->period_id);

                return $model->financialYears->number;
                //   ReportingPeriodMonth::find($model->month_range_period_id)->;
            })

            ->add('indicator_id')
            ->add('indicator', function ($model) {
                $model = SubmissionPeriod::find($model->period_id);
                $indicator = Indicator::find($model->indicator_id);
                return $indicator->indicator_name;
            })

            ->add('month_range', function ($model) {
                $model = SubmissionPeriod::find($model->period_id);

                return $model->reportingMonths->start_month . '-' . $model->reportingMonths->end_month;
                //
            })
            ->add('created_at')
            ->add('file_link', function ($model) {

                if ($model->file_link) {
                    $html = "
                       <a class='text-success custom-tooltip' title='download file' download='{$model->file_link}' href='" . asset('/storage/imports/' . $model->file_link) . "'>

                    <div class='d-flex align-items-center'>
                        <div class='flex-shrink-0 me-2'>
                            <div class='px-2 py-1 rounded-1 bg-success-subtle'>
                                <i class='fas fa-file-excel text-success'></i>
                            </div>
                        </div>
                        <div class='flex-grow-1 fw-bolder'>
                           {$model->file_link}

                        </div>
                    </div>

                        </a>";

                    return $html;
                }

                return null;
            })
            ->add('date_of_submission', fn($model) => $model->created_at != null ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s') : null)
            ->add('updated_at');
    }


    public function columns(): array
    {

        return [
            Column::make('#', 'rn')->sortable(),
            Column::make('File', 'file_link'),

            Column::make('Batch no', 'batch_no_formatted')
                ->sortable()
                ->searchable(),

            Column::make('SUBMITTED BY', 'username')->searchable(),

            Column::make('Organisation', 'organisation_formatted')->searchable(),
            Column::make('Form name', 'form_name')->searchable(),

            Column::make('Indicator', 'indicator')->searchable(),

            Column::make('SUBMISSION PERIOD', 'month_range')->searchable(),

            Column::make('Project Year', 'financial_year')->searchable(),
            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),

            // Column::make('Submission Period', 'reporting_period')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Comments', 'comments_truncated')->hidden(),

            Column::make('Date of submission', 'date_of_submission', 'created_at')
                ->sortable(),

            Column::action('Action'),
            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actions($row): array
    {

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->tooltip('Submit Approval')
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name' => 'view-submission-modal',

                ]),


            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can(
                    allowed: (User::find(auth()->user()->id)->hasAnyRole('manager') ||
                        User::find(auth()->user()->id)->hasAnyRole('admin'))
                )
                ->tooltip('Delete Data')
                ->dispatch('deleteBatch', [
                    'id' => $row->id,
                    'name' => 'delete-batch-modal'
                ]),
        ];
    }

    public function actionRules($row): array
    {
        $user = User::find(auth()->user()->id);

        return [
            //  Hide button edit for ID 1

            Rule::button('edit')
                ->when(fn($row) => $row->status !== 'pending')
                ->disable(),

            // Rule::button('delete')
            //     ->when(fn($row) => !($row->status === 'pending'))
            //     ->disable(),

            Rule::button('delete')
                ->when(fn($row) => !($user->hasAnyRole('manager')))
                ->disable(),

            Rule::rows()
                ->when(fn($row) => $row->batch_no === $this->batch)
                ->setAttribute('class', 'table-secondary'),

        ];
    }
}
