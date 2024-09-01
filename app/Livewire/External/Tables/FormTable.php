<?php

namespace App\Livewire\external\Tables;

use id;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Source;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\ResponsiblePerson;
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
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

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
        $personIds = ResponsiblePerson::where('organisation_id', $organisation_id)->pluck('id')->toArray();
        $sources = Source::whereIn('person_id', $personIds)->pluck('form_id')->unique();

        $myIndicators = ResponsiblePerson::where('organisation_id', $organisation_id)
            ->whereHas('sources')  // Ensure that the relationship 'sources' exists
            ->pluck('indicator_id')
            ->toArray();


        $query = SubmissionPeriod::with([
            'form',
            'form.indicators'
        ])->whereIn('indicator_id', $myIndicators);



        // // Query SubmissionPeriods with the necessary relationships
        // $query = SubmissionPeriod::with(['form', 'form.indicators'])
        //     ->whereHas('form.indicators.responsiblePeopleforIndicators', function (Builder $query) use ($organisation_id) {
        //         $query->where('organisation_id', $organisation_id);
        //     })

        // ;

        return $query;

    }


    #[On('timeout')]
    public function timeout()
    {
        SubmissionPeriod::where('date_ending', '<', Carbon::now())->update([
            'is_expired' => 1,
            'is_open' => 0
        ]);
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

                return ($model->is_open === 1 && $model->is_expired === 0) ? '<span class="badge bg-success">YES</span>' : '<span class="badge bg-danger">NO</span>';

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
                        return "<span class='text-danger'>Expired!</span>";
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
        $user = User::find($this->userId);
        $columns = [];


        $columns = [
            //     Column::make('Id', 'id'),
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



        ];

        // Conditionally add more columns if the user does not have the 'staff' role
        if ($user && !$user->hasAnyRole('staff')) {
            $columns[] = Column::make('Time remaining', 'remaining_days');
            $columns[] = Column::make('Submission status', 'submission_status');
        }

        $columns[] = Column::action('Action');
        return $columns;
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
        $user = Auth::user();
        $organisation = $user->organisation;
        $indicator = $form->indicators->where('id', $model->indicator_id)->first();
        $person = ResponsiblePerson::where('indicator_id', $indicator->id)->where('organisation_id', $organisation->id)->first();


        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $routePrefix = $this->currentRoutePrefix;

        if ($form->name == 'REPORT FORM') {


            $route = $routePrefix . '/forms/' . $project . '/aggregate/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;




        } else {
            $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/add/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;

        }

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

        $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/upload/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id . '/' . Uuid::uuid4()->toString();

        $this->redirect($route);
    }

    public function actionRules($row): array
    {

        $user = Auth::user();
        $organisationId = $user->organisation->id;
        $indicator = Indicator::find($row->indicator_id);

        $responsiblePeople = ResponsiblePerson::where('indicator_id', $row->indicator_id)
            ->where('organisation_id', $organisationId)
            ->first();

        // Check if the organisation has responsible people
        $hasResponsiblePeople = $responsiblePeople !== null;

        // Check if the responsible person has the required form
        $hasFormAccess = $hasResponsiblePeople ? $responsiblePeople->sources->where('form_id', $row->form_id)->isNotEmpty() : false;

        // Check if the organisation is responsible for the indicator
        $isOrganisationResponsible = $indicator->responsiblePeopleforIndicators->pluck('organisation_id')->contains($organisationId);
        return [



            Rule::button('add-data')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0)
                ->disable(),
            // Rules for uploading data

            // Rules for adding data
            Rule::button('add-data')
                ->when(fn() => $row->is_expired === 1 || $row->is_open === 0 || !$hasResponsiblePeople || !$hasFormAccess)
                ->disable(),

            Rule::button('upload')
                ->when(function ($row) {
                    $form = Form::find($row->form_id);

                    if ($form->name == 'REPORT FORM') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->disable(),
        ];
    }

    public function updated()
    {

        $this->dispatch('reload-tooltips');
    }

}
