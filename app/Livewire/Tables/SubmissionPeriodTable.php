<?php

namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
use Ramsey\Uuid\Uuid;

final class SubmissionPeriodTable extends PowerGridComponent
{
    use WithExport;
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

        return SubmissionPeriod::query()->orderBy('is_open', 'desc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);

                $form_name = str_replace(' ', '-', strtolower($form->name));
                $project = str_replace(' ', '-', strtolower($form->project->name));

                return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';

            })
            ->add('financial_year', function ($model) {
                return $model->financialYears->number;
                //   ReportingPeriodMonth::find($model->month_range_period_id)->;
            })

            ->add('indicator_id')
            ->add('indicator', function ($model) {
                $indicator = Indicator::find($model->indicator_id);
                return $indicator->indicator_name;
            })

            ->add('assigned', function ($model) {
                $indicator = Indicator::find($model->indicator_id);
                $responsiblePeople = $indicator->responsiblePeopleforIndicators->pluck('organisation_id');
                $oganisations = Organisation::whereIn('id', $responsiblePeople)->pluck('name');
                return implode(', ', $oganisations->toArray());

            })

            ->add('month_range', function ($model) {

                return $model->reportingMonths->start_month . '-' . $model->reportingMonths->end_month;
                //   ReportingPeriodMonth::find($model->month_range_period_id)->;
            })
            ->add('date_established_formatted', fn($model) => Carbon::parse($model->date_established)->format('d/m/Y'))
            ->add('date_ending_formatted', fn($model) => Carbon::parse($model->date_ending)->format('d/m/Y'))
            ->add('is_open')
            ->add('is_open_toggle', function ($model) {
                $open = $model->is_open === 1 ? 'bg-success' : 'bg-secondary';
                $is_open = $model->is_open === 1 ? 'Open' : 'Closed';

                return '<span class="badge ' . $open . ' "> ' . $is_open . '</span>';
            })
            ->add('is_expired')
            ->add('is_expired_toggle', function ($model) {
                $open = $model->is_expired === 1 ? 'bg-danger' : 'bg-secondary';
                $is_expired = $model->is_expired === 1 ? 'Yes' : 'No';

                return '<span class="badge ' . $open . ' "> ' . $is_expired . '</span>';
            })
            ->add('check_expiry', function ($model) {
                $getDate = Carbon::create($model->date_ending);
                if ($getDate->isPast()) {
                    SubmissionPeriod::find($model->id)->update([
                        'is_expired' => 1,
                        'is_open' => 0,
                    ]);

                    $this->refreshData();
                }
            })
            ->add('submissions', fn($model) => '<span class=" fw-bold">' . SubmissionPeriod::find($model->id)->submissions->count() . '</span>')
            ->add('submission_batch', fn($model) => SubmissionPeriod::find($model->id)->submissions->where('batch_type', 'batch')->count())
            ->add('submission_aggregate', fn($model) => SubmissionPeriod::find($model->id)->submissions->where('batch_type', 'aggregate')->count())
            ->add('submission_manual', fn($model) => SubmissionPeriod::find($model->id)->submissions->where('batch_type', 'manual')->count())
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Form', 'form_name')
                ->headerAttribute(classAttr: 'table-sticky-col')
                ->bodyAttribute(classAttr: 'table-sticky-col')
            ,

            Column::make('Start of Submissions', 'date_established_formatted', 'date_established')
                ->sortable(),

            Column::make('End of Submissions', 'date_ending_formatted', 'date_ending')
                ->sortable(),

            Column::make('Status', 'is_open_toggle')
                ->sortable()
                ->searchable(),
            Column::make('Months', 'month_range')
            ,

            Column::make('Financial Year', 'financial_year')
                ->sortable()
                ->searchable(),

            Column::make('Indicator', 'indicator')
            ,

            Column::make('Assigned Organisations', 'assigned')
            ,

            Column::make('Expired', 'is_expired_toggle')
                ->sortable()
                ->searchable(),

            Column::make('Submissions', 'submissions')
                ->sortable()
                ->searchable(),
            Column::make('Submission/Batch', 'submission_batch')
                ->sortable()
                ->searchable(),

            Column::make('Submissions/Aggregate', 'submission_aggregate')
                ->sortable()
                ->searchable(),

            Column::make('Submissions/Manual', 'submission_manual')
                ->sortable()
                ->searchable(),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datetimepicker('date_established'),
            // Filter::datetimepicker('date_ending'),
        ];
    }
    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
        $this->dispatch('reload-tooltips');
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


    #[On('sendFollowUpData')]
    public function sendFollowUpData($model)
    {
        $model = (object) $model;

        $form = Form::find($model->form_id);
        $user = Auth::user();
        $organisation = $user->organisation;
        $indicator = $form->indicators->where('id', $model->indicator_id)->first();
        $checkTypeofSubmission = ResponsiblePerson::where('indicator_id', $indicator->id)->where('organisation_id', $organisation->id)->pluck('type_of_submission');

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $routePrefix = $this->currentRoutePrefix;

        if ($checkTypeofSubmission->contains('aggregate')) {


            $route = $routePrefix . '/forms/' . $project . '/aggregate/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;




        } else {
            $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id;

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

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->tooltip('Edit Record')
                ->class('btn btn-primary goUp btn-sm my-1')
                ->dispatch('editData', ['rowId' => $row->id]),

            Button::add('add-data')
                ->slot('<i class="bx bx-plus"></i>')
                ->id()
                ->class('btn btn-primary btn-sm my-1')
                ->tooltip('Add Data')
                ->dispatch('sendData', ['model' => $row]),

            Button::add('upload')
                ->slot('<i class="bx bx-upload"></i>')
                ->id()
                ->tooltip('Upload Your Data')
                ->class('btn btn-primary my-1 btn-sm')
                ->dispatch('sendUploadData', ['model' => $row]),


        ];
    }
    // public function actionsFromView($row): View
    // {
    //     return view('livewire.submission-view', ['row' => $row]);
    // }

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
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0)
                ->disable(),

            // Rules for adding data
            Rule::button('add-data')
                ->when(fn() => $row->is_expired === 1 || $row->is_open === 0 || !$hasResponsiblePeople || !$hasFormAccess)
                ->disable(),

            // Rules for uploading data
            Rule::button('upload')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0 || !$isOrganisationResponsible ||
                    ($row->form_id && in_array(Form::find($row->form_id)->name, ['REPORT FORM', 'SCHOOL RTC CONSUMPTION FORM'])))
                ->disable(),
        ];
    }

    public function updated()
    {

        $this->dispatch('reload-tooltips');
    }

}