<?php

namespace App\Livewire\Tables;

use App\Helpers\TruncateText;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use App\Traits\DownloadImportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
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
    //Batch Submission Table
    use DownloadImportTrait;
    use WithExport;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public $batch;
    public $row = 1;
    public string $sortField = 'submission_id';
    public string $primaryKey = 'submission_id';
    public string $sortDirection = 'desc';
    public string $tableName = 'SubmissionsTable';
    public $routePrefix;
    public function setUp(): array
    {
        $this->showCheckBox();
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
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()->pageName('submissionPage')
                ->showRecordCount(),
        ];
    }






    public function datasource(): Builder
    {
        $query = Submission::query()
            ->join('forms', 'forms.id', '=', 'submissions.form_id')
            ->join('submission_periods', 'submissions.period_id', '=', 'submission_periods.id')
            ->leftJoin('users', 'users.id', '=', 'submissions.user_id')
            ->join('organisations', 'users.organisation_id', '=', 'organisations.id') //->join('users', 'users.id', '=', 'submissions')
            ->with([
                'period.indicator',
                'user' => fn($q) => $q->withTrashed(),
                'user.organisation',
                'period.reportingMonths',
                'form',
                'period.financialYears'
            ])
            ->where('batch_type', 'batch')->select([
                'submissions.*',
                'submissions.id as submission_id',
                'users.name as username',
                'forms.name as form_name',
                'organisations.name as organisation_name',
                'organisations.id as organisation_id',
                'submission_periods.id as period_id',
                'submission_periods.month_range_period_id as month_range_period_id',


                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return $query->where('users.id', $user->id);
        }

        return $query;
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'submissions.status')
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
    public function relationSearch(): array
    {
        return [

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

            'period.financialYears' => [
                'number',
            ],
            'form' => [
                'name',
            ],




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


                $text = $model->batch_no;

                $html = '';

                $html .= '

<div>
<div class="accordion accordion-flush" id="default-accordion-example-' . $model->batch_no . '_' . $model->id . '">
    <div class="border accordion-item custom-tooltip" title="show batch number">
        <h2 class=" accordion-header" id="headingOne" >
            <button class="p-1 accordion-button collapsed " style="font-size:0.85rem"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $model->batch_no . '_' . $model->id . '" aria-expanded="true" aria-controls="collapse-' . $model->batch_no . '_' . $model->id . '">
<i class="bx bx-dots-horizontal-rounded"></i>
            </button>
        </h2>
        <div id="collapse-' . $model->batch_no . '_' . $model->id . '" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#default-accordion-example-' . $model->batch_no . '_' . $model->id . '">
            <div class="accordion-body">
                ' . $text . '
            </div>
        </div>
        </div>

</div>
</div>
';
                return $html;
                // return $model->batch_no;
            })
            ->add('user_id')
            ->add('username', function ($model) {

                return $model->user?->name;
            })
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);


                return $form->name;
            })
            ->add('organisation')
            ->add('organisation_formatted', function ($model) {

                return $model->user?->organisation?->name;
            })
            ->add('status')
            ->add('batch_type')
            ->add('record_filter', function ($model) {})
            ->add('status_formatted', function ($model) {
                $model->status = $model->status === 'denied' ? 'disapproved' : $model->status;
                if ($model->status === 'approved') {
                    return '<span class="badge bg-success-subtle text-success">' . $model->status . '</span>';
                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning-subtle text-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-danger-subtle text-danger">disapproved</span>';
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
                $html = '';
                $disabled = !$model->comments ? 'pe-none opacity-25' : '';
                $html .= '

<div class="' . $disabled . '">
<div class="accordion accordion-flush" id="default-accordion-example-' . $model->id . '">
    <div class="border accordion-item custom-tooltip " title="show comments">
        <h2 class=" accordion-header" id="headingOne" >
            <button class="p-1 accordion-button collapsed " style="font-size:0.85rem"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $model->id . '" aria-expanded="true" aria-controls="collapse-' . $model->id . '">
<i class="bx bx-dots-horizontal-rounded"></i>
            </button>
        </h2>
        <div id="collapse-' . $model->id . '" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#default-accordion-example-' . $model->id . '">
            <div class="accordion-body">
                ' . $text . '
            </div>
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
            Column::make('#', 'rn')->sortable()->hidden(),
                   Column::make('Batch no', 'batch_no_formatted','submissions.batch_no')

                ->searchable(),
            Column::make('File', 'file_link'),


            Column::make('Form name', 'form_name')->searchable(),



            Column::make('SUBMITTED BY', 'username')->searchable(),

            Column::make('Organisation', 'organisation_formatted')->searchable(),



            Column::make('SUBMISSION PERIOD', 'month_range')->searchable(),

            Column::make('Project Year', 'financial_year')->searchable(),
            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),

            // Column::make('Submission Period', 'reporting_period')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Comments', 'comments_truncated'),
            Column::make('Description', 'description')->searchable(),
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
                ->slot('<i class="bx bx-analyse"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->tooltip('Review Submission')
                ->dispatch('showModal', [
                    'id' => $row->id,
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
   #[On('view-data-submission')]
    public function viewData($batch_no, $form_name)
    {
        $routePrefix = $this->routePrefix ;
        $project = Project::where('name', 'RTC MARKET')->first()->name;

        if (!$routePrefix || !$project || !$form_name || !$batch_no) {
            $this->alert('error', 'Missing parameters to view data', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false
            ]);
            return;
        }
        $form_name = strtolower(str_replace(' ', '-', $form_name));
        $project = strtolower(str_replace(' ', '-', $project));
        $route = "{$routePrefix}/forms/{$project}/{$form_name}/view/{$batch_no}";
        return redirect($route);
    }

    public function actionRules(): array
    {
        $user = User::find(auth()->user()->id);

        return [
            //  Hide button edit for ID 1

            Rule::button('edit')
                ->when(fn($row) => $row->status == 'denied' || $row->status == 'disapproved')
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

            Rule::button('bulk-download')
                ->when(fn() => true)
                ->disable(),

        ];
    }
}
