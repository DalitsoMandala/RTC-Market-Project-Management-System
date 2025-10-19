<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\Organisation;
use App\Helpers\TruncateText;
use App\Models\FinancialYear;
use Illuminate\Support\Carbon;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\DB;
use App\Traits\DownloadImportTrait;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
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

final class AggregateSubmissionTable extends PowerGridComponent
{
    use WithExport;
    use DownloadImportTrait;
    use LivewireAlert;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public $row = 1;
    public $batch;
    public string $sortField = 'submission_id';
    public string $sortDirection = 'desc';
    public string $primaryKey = 'submission_id';

    public function updatedCheckboxValues($values)
    {
        // logger('Selected IDs: ', $values);
    }
    public function setUp(): array
    {
        $route = Route::current();
        $parameters = $route->parameters();
        $collection = collect($parameters);
        if ($collection->has('batch')) {
            $this->batch = $collection->get('batch');
        }

        $this->showCheckBox('submission_id');
        $this->setLocation('exports');
        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()->pageName('AggregatePage')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        // $query = Submission::query()
        //     ->with(['period.indicator', 'user.organisation', 'user', 'period.reportingMonths', 'form', 'period.financialYears'])
        //     ->where('batch_type', 'aggregate');

        // $user = User::find(auth()->user()->id);
        // $organisation_id = $user->organisation->id;

        // if ($user->hasAnyRole('external')) {
        //     return $query->where('user_id', $user->id);
        // }

        // return $query->select([
        //     '*',
        //     DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        // ]);

        $query = Submission::query()
            ->join('forms', 'forms.id', '=', 'submissions.form_id')
            ->join('submission_periods', 'submissions.period_id', '=', 'submission_periods.id')
            ->join('users', 'users.id', '=', 'submissions.user_id')
            ->join('submission_reports', 'submissions.batch_no', '=', 'submission_reports.uuid')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id') //->join('users', 'users.id', '=', 'submissions')
            ->with(['period.indicator', 'user.organisation', 'user',  'period.reportingMonths', 'form', 'period.financialYears'])
            ->where('batch_type', 'aggregate')->select([
                'submissions.*',
                'submissions.id as submission_id',
                'users.name as username',
                'forms.name as form_name',
                'organisations.name as organisation_name',
                'organisations.id as organisation_id',
                'submission_periods.id as period_id',
                'submission_periods.month_range_period_id as month_range_period_id',


                DB::Raw('ROW_NUMBER() OVER (ORDER BY submissions.id) AS rn')
            ]);

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return $query->where('user_id', $user->id);
        }

        return $query;
    }



    public function relationSearch(): array
    {
        return [
            'period.indicator' => [  // relationship on dishes model
                'indicator_name',  // column enabled to search
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
                'financialYears.number'
            ],
            'form' => [
                'name',
            ],

        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'status')
                ->dataSource(function () {
                    $submission = Submission::select(['status'])->distinct();

                    return $submission->get()->map(function ($submission) {

                        return [
                            'status' => $submission->status === 'denied' ? 'disapproved' : $submission->status,
                            'value' => $submission->status
                        ];
                    });
                })
                ->optionLabel('status')
                ->optionValue('value'),

            Filter::select('form_name', 'forms.id')
                ->dataSource(function () {
                    $submission = Form::select(['name', 'id'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('organisation_formatted', 'organisations.id')
                ->dataSource(function () {
                    $submission = Organisation::select(['name', 'id'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('name')
                ->optionValue('id'),



            Filter::select('month_range', 'submission_periods.month_range_period_id')
                ->dataSource(function () {
                    $submission = ReportingPeriodMonth::select(['start_month', 'end_month', 'id'])->distinct();

                    return $submission->get()->map(function ($submission) {

                        return [
                            'name' => $submission->start_month . ' - ' . $submission->end_month,
                            'id' => $submission->id
                        ];
                    });
                })
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('financial_year', 'submission_periods.financial_year_id')
                ->dataSource(function () {
                    $submission = FinancialYear::select(['id', 'number'])->distinct();

                    return $submission->get()->map(function ($submission) {

                        return [
                            'number' => 'Year ' . $submission->number,
                            'id' => $submission->id
                        ];
                    });
                })
                ->optionLabel('number')
                ->optionValue('id'),


        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', fn($model) => $this->row++)
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

                $form_name = str_replace(' ', '-', strtolower($form->name));
                $project = str_replace(' ', '-', strtolower($form->project->name));

                // return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';
                return $form->name . '-' . $model->id;
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

                if (!$model->comments) {
                    return '<span class="badge bg-success-subtle text-success">No comment</span></span>';
                }
                $text = $model->comments;
                $trunc = new TruncateText($text, 30);
                $html = '';
                $html .= '

<!-- Base Example -->
<div class="accordion" id="default-accordion-example-' . $model->id . '">
    <div class="shadow accordion-item custom-tooltip" title="show comments">
        <h2 class="accordion-header " id="headingOne" >
            <button class="p-1 accordion-button collapsed " style="font-size:0.55rem"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $model->id . '" aria-expanded="true" aria-controls="collapse-' . $model->id . '">
               Comments
            </button>
        </h2>
        <div id="collapse-' . $model->id . '" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#default-accordion-example-' . $model->id . '">
            <div class="accordion-body">
                ' . $text . '
            </div>
        </div>
        </div>

</div>

';
                return $html;
            })
            ->add('financial_year', function ($model) {
                $model = SubmissionPeriod::find($model->period_id);

                return $model->financialYears->number;
                //   ReportingPeriodMonth::find($model->month_range_period_id)->;
            })
            ->add('indicator_id')
            ->add('indicator', function ($model) {

                $period = SubmissionReport::with('indicator')->where('uuid', $model->batch_no)->first();
                return $period?->indicator->indicator_name;
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
                       <a class='text-success custom-tooltip' title='download file' download='{$model->file_link}' href='" . asset('/storage/exports/' . $model->file_link) . "'>

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
                ->searchable()->hidden(),


            Column::make('Form name', 'form_name')->searchable(),
            Column::make('Indicator', 'indicator')->searchable()->headerAttribute(styleAttr: "min-width:350px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('SUBMITTED BY', 'username')->searchable(),
            Column::make('Organisation', 'organisation_formatted')->searchable(),
            Column::make('SUBMISSION PERIOD', 'month_range')->searchable(),
            Column::make('Project Year', 'financial_year')->searchable(),
            Column::make('Status', 'status_formatted')
                ->searchable(),
            // Column::make('Submission Period', 'reporting_period')
            //     ->sortable()
            //     ->searchable(),
            Column::make('Comments', 'comments_truncated'),
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
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('Edit Data')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('showAggregate', [
                    'id' => $row->id,
                    'name' => 'view-aggregate-modal'
                ]),
            Button::add('show')
                ->slot('<i class="fas fa-eye"></i>')
                ->id()
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('View Data')
                ->dispatch('showDataAggregate', [
                    'id' => $row->id,
                    'name' => 'view-data-agg-modal'
                ]),
            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red btn-sm my-1 custom-tooltip')
                ->tooltip('Delete Data')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('deleteAggregate', [
                    'id' => $row->id,
                    'name' => 'delete-aggregate-modal'
                ]),
        ];
    }

    public function actionRules($row): array
    {
        $user = User::find(auth()->user()->id);

        return [
            // Hide button edit for ID 1
            // Rule::button('show')
            //     ->when(fn($row) => ($row->status === 'pending')) // if admin or manager hide the show button
            //     ->disable(),
            Rule::button('edit')
                ->when(fn($row) => $row->status === 'denied')
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => $row->status === 'pending')
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => !$user->hasAnyRole('manager')
                    || !$user->hasAnyRole('admin'))
                ->disable(),

            Rule::rows()
                ->when(fn($row) => $row->batch_no === $this->batch)
                ->setAttribute('class', 'table-secondary'),
        ];
    }
}
