<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Submission;
use App\Helpers\TruncateText;
use Illuminate\Support\Carbon;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
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

final class AggregateSubmissionTable extends PowerGridComponent
{
    use WithExport;
    public $filter;
    public $userId;
    public bool $showFilters = true;

    public function setUp(): array
    {


        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
    public function datasource(): Builder
    {
        $query = Submission::query()->with('period.indicator')->where('batch_type', 'aggregate');

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return Submission::query()->with('period.indicator')->where('batch_type', 'aggregate')->where('user_id', $user->id);
        }

        return $query;
    }

    public function relationSearch(): array
    {
        return [
            'period.indicator' => [ // relationship on dishes model
                'indicator_name', // column enabled to search

            ],





        ];
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
            Filter::inputText('batch_no_formatted', 'batch_no'),
            Filter::inputText('indicator')->filterRelation('period.indicator', 'indicator_name'),

        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

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
                    return '<span class="badge bg-success">' . $model->status . '</span>';
                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-theme-red">' . $model->status . '</span>';
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
                    return '<a  data-bs-toggle="tooltip" data-bs-title="download file" download="' . $model->file_link . '" href="' . asset('/storage/imports') . '/' . $model->file_link . '"><i class="fas fa-file-excel"></i>' . $model->file_link . '</a>';
                }

                return null;
            })
            ->add('date_of_submission', fn($model) => $model->created_at != null ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s') : null)
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Batch no', 'batch_no_formatted')
                ->sortable()
                ->searchable(),

            Column::make('SUBMITTED BY', 'username'),

            Column::make('Organisation', 'organisation_formatted'),
            Column::make('Form name', 'form_name'),

            Column::make('Indicator', 'indicator'),

            Column::make('SUBMISSION PERIOD', 'month_range'),

            Column::make('Project Year', 'financial_year'),
            Column::make('Status', 'status_formatted')

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
                ->slot('<i class="bx bx-pen"></i> Edit')
                ->id()
                ->class('btn btn-warning my-1')
                ->can(allowed: (User::find(auth()->user()->id)->hasAnyRole('internal') && User::find(auth()->user()->id)->hasAnyRole('manager')) || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('showAggregate', [
                    'id' => $row->id,
                    'name' => 'view-aggregate-modal'
                ]),

            Button::add('show')
                ->slot('<i class="fas fa-eye"></i>')
                ->id()
                ->class('btn btn-warning my-1')
                ->tooltip('View Data')
                ->dispatch('showDataAggregate', [
                    'id' => $row->id,
                    'name' => 'view-data-agg-modal'
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash"></i> Delete')
                ->id()
                ->class('btn btn-theme-red my-1')
                ->can(allowed: (User::find(auth()->user()->id)->hasAnyRole('internal') && User::find(auth()->user()->id)->hasAnyRole('manager')) || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('deleteAggregate', [
                    'id' => $row->id,
                    'name' => 'delete-aggregate-modal'
                ]),

        ];
    }

    public function actionRules($row): array
    {


        return [
            // Hide button edit for ID 1
            Rule::button('show')
                ->when(fn($row) => (User::find(auth()->user()->id)->hasAnyRole('internal') && User::find(auth()->user()->id)->hasAnyRole('manager')) || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->hide(),


            Rule::button('edit')
                ->when(fn($row) => User::find(auth()->user()->id)->hasAnyRole('external') || User::find(auth()->user()->id)->hasAnyRole('staff'))
                ->hide(),

            Rule::button('delete')
                ->when(fn($row) => User::find(auth()->user()->id)->hasAnyRole('external') || User::find(auth()->user()->id)->hasAnyRole('staff'))
                ->disable(),


            Rule::button('delete')
                ->when(fn($row) => $row->status !== 'pending')
                ->disable(),

            // Rule::button('edit')
            //     ->when(fn($row) => $row->status == 'denied')
            //     ->disable(),

            Rule::button('edit')
                ->when(fn($row) => $row->status == 'denied')
                ->disable(),
            Rule::button('delete')
                ->when(fn($row) => $row->status == 'denied')
                ->disable(),
        ];
    }
}
