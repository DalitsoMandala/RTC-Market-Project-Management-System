<?php

namespace App\Livewire\Tables;

use App\Helpers\TruncateText;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    use WithExport;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {

        if ($this->userId) {
            $user = Auth::user();
            if ($user->hasAnyRole('external')) {
                return Submission::query()->where('user_id', $user->id)->where('batch_type', $this->filter)->select([
                    '*',
                    DB::raw(' ROW_NUMBER() OVER (ORDER BY id) as row_num'),
                ])->orderBy('created_at', 'desc');

            } else {
                return Submission::query()->where('batch_type', $this->filter)->select([
                    '*',
                    DB::raw(' ROW_NUMBER() OVER (ORDER BY id) as row_num'),
                ])->orderBy('created_at', 'desc');

            }
        }
        return Submission::query()->where('batch_type', $this->filter)->select([
            '*',
            DB::raw(' ROW_NUMBER() OVER (ORDER BY id) as row_num'),
        ])->orderBy('created_at', 'desc');
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'status')
                ->dataSource(function () {
                    $submission = Submission::select(['status'])->distinct();
                    // $submissionArray = [];

                    // foreach($submission as $index => $status){

                    // }
                    // dd($submission->get());
                    return $submission->get();
                })
                ->optionLabel('status')
                ->optionValue('status')
            ,

        ];

    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('row_num')
            ->add('batch_no')
            ->add('batch_no_formatted', function ($model) {

                $form = Form::find($model->form_id);
                $form_name = $form->name;
                $project = $form->project->name;
                $project_name = strtolower(str_replace(' ', '-', $project));

                $formatted_name = strtolower(str_replace(' ', '-', $form_name));
                $user = Auth::user();
                $status = $model->status === 'pending' ? 'disabled text-secondary pe-none' : '';

                if ($model->batch_type == 'aggregate') {

                    return '<a wire:click="$dispatch(\'showAggregate\', { id: ' . $model->id . ', name : `view-aggregate-modal` })" data-bs-toggle="tooltip" data-bs-title="View batch"  href="#">' . $model->batch_no . '</a>';



                } else if ($model->batch_type == 'batch') {

                    return '<a data-bs-toggle="tooltip" data-bs-title="ViewW"  class="pe-none text-muted" href="forms/' . $project_name . '/' . $formatted_name . '/' . $model->batch_no . '/view">' . $model->batch_no . '</a>';
                } else {

                    return '<a class="' . $status . '">' . $model->batch_no . '</a>';
                }

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

                return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';

            })
            ->add('organisation')
            ->add('organisation_formatted', function ($model) {

                $user = User::find($model->user_id);

                return $user->organisation->name;

            })
            ->add('status')
            ->add('batch_type')
            ->add('record_filter', function ($model) {

            })
            ->add('status_formatted', function ($model) {

                if ($model->status === 'approved') {
                    return '<span class="badge bg-success">' . $model->status . '</span>';

                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-danger">' . $model->status . '</span>';
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
            //  Column::make('Id', 'row_num'),
            Column::make('Batch no', 'batch_no_formatted')
                ->sortable()
                ->searchable(),

            Column::make('SUBMITTED BY', 'username'),

            Column::make('Organisation', 'organisation_formatted'),
            Column::make('Form name', 'form_name'),

            Column::make('Indicator', 'indicator'),

            Column::make('SUBMISSION PERIOD', 'month_range')
            ,

            Column::make('Project Year', 'financial_year'),
            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),

            // Column::make('Submission Period', 'reporting_period')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Comments', 'comments_truncated')->hidden(),

            Column::make('Date of submission', 'date_of_submission', 'created_at')
                ->sortable(),
            Column::make('File', 'file_link'),
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
                ->class('btn btn-primary')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('internal'))
                ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-submission-modal']),
        ];
    }

    public function actionRules($row): array
    {

        $user = Auth::user();
        return [

            Rule::button('edit')
                ->when(fn($row) => ($row->status === 'pending' && !$user->hasAnyRole('organiser')) || ($row->is_complete === 1) || ($row->user_id === auth()->user()->id))
                ->disable(),

            // Rule::button('edit')
            //     ->when(fn($row) => $row->is_complete === 1)
            //     ->disable(),
        ];
    }

}
