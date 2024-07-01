<?php

namespace App\Livewire\external\Tables;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Carbon\Carbon;
use id;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class FormTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public $currentRoutePrefix;
    public function setUp(): array
    {
        //  $this->showCheckBox();

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
        $user = User::find($this->userId);

        $organisation_id = $user->organisation->id;

        $myIndicators = ResponsiblePerson::where('organisation_id', $organisation_id)->pluck('indicator_id')->toArray();
        $query = SubmissionPeriod::with(['form', 'form.indicators'])->whereIn('indicator_id', $myIndicators);


        // // Query SubmissionPeriods with the necessary relationships
        // $query = SubmissionPeriod::with(['form', 'form.indicators'])
        //     ->whereHas('form.indicators.responsiblePeopleforIndicators', function (Builder $query) use ($organisation_id) {
        //         $query->where('organisation_id', $organisation_id);
        //     })

        // ;

        return $query;

    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_formatted', function ($model) {

                $form = Form::find($model->form->id);

                $form_name = str_replace(' ', '-', strtolower($form->name));

                $project = str_replace(' ', '-', strtolower($form->project->name));
                return $form->name;
                //  return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';
    
            })
            ->add('type')
            ->add('open_for_submission', function ($model) {

                return ($model->is_open === 1 && $model->is_expired === 0) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>';

            })
            ->add('project_id')
            ->add('project', function ($model) {
                $form = Form::find($model->form->id);
                return $form->project->name;

            })

            ->add('submission_duration', function ($model) {

                $date_start = Carbon::parse($model->date_established)->format('d F Y') ?? null;
                $date_end = Carbon::parse($model->date_ending)->format('d F Y') ?? null;
                return "{$date_start} - {$date_end}";

            })

            ->add('remaining_days', function ($model) {
                $date = Carbon::create($model->date_ending);
                $now = Carbon::now();
                $date_end = $date->diffForHumans() ?? null;

                if ($date->isPast()) {

                    return "<span class='text-danger'>Expired!</span>";
                } else {
                    if ($model->is_expired === 1 && !$date->isPast()) {
                        return "<span class='text-danger'>Cancelled!</span>";
                    } else {
                        return "<b>{$date_end}</b>";
                    }

                }

            })
            ->add('submission_status', function ($model) {
                $userId = $this->userId;

                $submitted = Submission::where('user_id', $userId)
                    ->where('period_id', $model->id)
                    ->where('form_id', $model->form->id)->count();

                if ($submitted === 0) {
                    return '<span class="badge bg-danger">Not submitted</span>';
                } else {
                    return '<span class="badge bg-success">Submitted</span>';

                }
            })
            ->add('financial_year', fn($model) => FinancialYear::find($model->financial_year_id)->number)
            ->add('submission_period', fn($model) => ReportingPeriodMonth::find($model->month_range_period_id)->start_month . '-' .
                ReportingPeriodMonth::find($model->month_range_period_id)->end_month)
            ->add('indicator_id')
            ->add('indicator', function ($model) {
                $indicator = Indicator::find($model->indicator_id);
                return $indicator->indicator_name;
            })
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name_formatted', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Project', 'project')
                ->sortable(),

            Column::make('Open for submission', 'open_for_submission')
            ,

            Column::make('Indicator', 'indicator'),

            Column::make('Submission Period', 'submission_period')
            ,

            Column::make('Financial Year', 'financial_year')
            ,
            Column::make('Submission Dates', 'submission_duration')
            ,
            Column::make('Time remaining', 'remaining_days'),

            Column::make('Submission status', 'submission_status'),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [

        ];
    }

    #[On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        return [

            Button::add('add-data')
                ->slot('<i class="bx bx-plus"></i>')
                ->id()
                ->class('btn btn-primary my-1')
                ->tooltip('Add Manual Data')
                ->dispatch('sendData', ['model' => $row]),

            Button::add('upload')
                ->slot('<i class="bx bx-upload"></i>')
                ->id()
                ->tooltip('Upload Your Data')
                ->class('btn btn-primary my-1')
                ->dispatch('sendUploadData', ['model' => $row]),

        ];
    }

    #[On('sendData')]
    public function sendData($model)
    {
        $model = (object) $model;

        $form = Form::find($model->form_id);

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $routePrefix = $this->currentRoutePrefix;

        $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/add/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;

        $this->redirect($route);
    }

    #[On('sendUploadData')]
    public function sendUploadData($model)
    {
        $model = (object) $model;

        $form = Form::find($model->form_id);

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $routePrefix = $this->currentRoutePrefix;

        $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/upload/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;

        $this->redirect($route);
    }

    public function actionRules($row): array
    {
        return [

            Rule::rows()
                ->when(fn($model) => $model->is_open === 0)
                ->setAttribute('class', 'table-light pe-none opacity-50'),

            Rule::button('add-data')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0)
                ->disable(),
            Rule::button('upload')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0)
                ->disable(),
        ];
    }

    public function updated()
    {

        $this->dispatch('reload-tooltips');
    }

}
