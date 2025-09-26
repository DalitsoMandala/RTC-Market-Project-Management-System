<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\Organisation;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Submission;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\SubmissionPeriod;
use App\Models\ResponsiblePerson;
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

final class SubmissionPeriodFormTable extends PowerGridComponent
{
    use WithExport;
    public $indicatorIds = [];
    public $formIds = [];
    public array $submissionPeriodRow;
    public $currentRoutePrefix;

    public function setUp(): array
    {

        $data = $this->submissionPeriodRow;





        $submissionPeriods = SubmissionPeriod::where('date_established', $data['date_established'])
            ->where('date_ending', $data['date_ending'])
            ->where('is_expired', $data['is_expired'])
            ->where('is_open', $data['is_open'])
            ->where('financial_year_id', $data['financial_year_id'])
            ->where('month_range_period_id', $data['month_range_period_id'])

            ->pluck('form_id')
            ->unique()
            ->values()->toArray();



        $this->formIds = $submissionPeriods;


        return [];
    }

    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $myIndicators = ResponsiblePerson::where('organisation_id', $organisation_id)
            // Ensure that the relationship 'sources' exists
            ->pluck('indicator_id')
            ->toArray();



        $query =  Form::query()->with('indicators')
            ->whereHas('indicators', function ($query) use ($myIndicators) {
                $query->whereIn('indicators.id', $myIndicators);
            })
            ->whereIn('id', $this->formIds)->select([
                '*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('type')
            ->add('slug')
            ->add('indicators', function ($model) {

                $indicators = $model->indicators->pluck('indicator_name')->toArray();
                return Str::limit(implode(', ', $indicators), 50);
            })
            ->add('submisssions', function ($model) {
                $data = $this->submissionPeriodRow;
                $submissions = Submission::with(['form', 'period'])->whereHas(
                    'form',
                    function (Builder $query) use ($model) {
                        $query->where('id', $model->id);
                    }
                )->whereHas('period', function ($query) use ($data) {
                    $query->where('date_established', $data['date_established'])
                        ->where('date_ending', $data['date_ending'])
                        ->where('is_expired', $data['is_expired'])
                        ->where('is_open', $data['is_open'])
                        ->where('financial_year_id', $data['financial_year_id'])
                        ->where('month_range_period_id', $data['month_range_period_id']);
                })->get();
                $counts = $submissions->groupBy('batch_type')->map->count();

                $batch = $counts['batch'] ?? 0;
                $manual = $counts['manual'] ?? 0;
                $aggregate = $counts['aggregate'] ?? 0;

                if ($model->name == 'REPORT FORM') {
                    return <<<HTML
                      <span class=" me-1">Aggregate: <b>$aggregate</b></span>
                    HTML;
                }

                return <<<HTML
        <span class=" me-1">Batch: <b>$batch</b></span> |
        <span class=" me-1">Manual: <b>$manual</b> </span>

    HTML;
            })
            ->add('status', function ($model) {
                $data = $this->submissionPeriodRow;
                $userId = auth()->id(); // Or pass in if needed
                $userOrganisation = User::find($userId)->organisation->id;
                $usersForTHisOrganisation = Organisation::with('users')->find($userOrganisation);
                $users = $usersForTHisOrganisation->users->pluck('id')->toArray();
                $hasSubmission = Submission::whereIn('user_id', $users)
                    ->whereHas('form', function (Builder $query) use ($model) {
                        $query->where('id', $model->id);
                    })
                    ->whereHas('period', function (Builder $query) use ($data) {
                        $query->where('date_established', $data['date_established'])
                            ->where('date_ending', $data['date_ending'])
                            ->where('is_expired', $data['is_expired'])
                            ->where('is_open', $data['is_open'])
                            ->where('financial_year_id', $data['financial_year_id'])
                            ->where('month_range_period_id', $data['month_range_period_id']);
                    })
                    ->exists();

                if ($hasSubmission) {
                    return '<span class="badge text-success bg-soft-success">Submitted</span>';
                }

                return '<span class="badge text-danger bg-soft-danger">Not Submitted</span>';
            })

            ->add('project_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        $user = User::find(auth()->user()->id);
        if ($user->hasAnyRole('external')) {
            return [
                Column::make('Id', 'rn'),
                Column::make('Name', 'name'),
                Column::make('Submission Status', 'status'),

                Column::action('')
            ];
        }
        return [
            Column::make('Id', 'rn')->headerAttribute('table-secondary table-bordered'),
            Column::make('Name', 'name')->headerAttribute('table-secondary table-bordered'),
            Column::make('Submission Status', 'status')->headerAttribute('table-secondary table-bordered'),
            Column::make('Submissions', 'submisssions')->headerAttribute('table-secondary table-bordered'),
            Column::action('')->headerAttribute('table-secondary table-bordered'),

        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
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
                ->class('btn btn-warning btn-sm my-1 custom-tooltip')
                ->tooltip('Add Data')
            


                ->dispatch('sendData', ['model' => $row]),

            Button::add('upload')
                ->slot('<i class="bx bx-upload"></i>')
                ->id()
                ->tooltip('Upload Your Data')
                ->class('btn btn-warning my-1 btn-sm custom-tooltip')
                ->dispatch('sendData', ['model' => $row, 'upload' => true]),
        ];
    }
    #[On('sendData')]
    public function sendData($model, $upload = false)
    {

        $model = (object) $model;
        $submissionPeriodRow = $this->submissionPeriodRow;
        $form = Form::find($model->id);

        $data = $this->submissionPeriodRow;

        $submissionPeriods = SubmissionPeriod::where('date_established', $data['date_established'])
            ->where('date_ending', $data['date_ending'])
            ->where('is_expired', $data['is_expired'])
            ->where('is_open', $data['is_open'])
            ->where('financial_year_id', $data['financial_year_id'])
            ->where('month_range_period_id', $data['month_range_period_id'])
            ->where('form_id', $model->id)
            ->pluck('id')
            ->random(1)
            ->toArray();


        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));



        $routePrefix = $data['routePrefix'];

        $route = "";

        if ($upload === true) {

            $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/upload/' . $model->id . '/0/' . $submissionPeriodRow['financial_year_id'] . '/' . $submissionPeriodRow['month_range_period_id'] . '/' .  $submissionPeriods[0] . '/' . Uuid::uuid4()->toString();
            $this->redirect($route);
        } else {
            if ($form->name == 'REPORT FORM') {


                $route = $routePrefix . '/forms/' . $project . '/aggregate/' . $model->id . '/0/' . $submissionPeriodRow['financial_year_id'] . '/' . $submissionPeriodRow['month_range_period_id'] . '/' . $submissionPeriods[0];
            } else {

                $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/add/' . $model->id . '/0/' . $submissionPeriodRow['financial_year_id'] . '/' . $submissionPeriodRow['month_range_period_id'] . '/' . $submissionPeriods[0];
            }

            $this->redirect($route);
        }
    }



    #[On('sendUploadData')]
    public function sendUploadData($model)
    {
        $model = (object) $model;
        +$form = Form::find($model->form_id);

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $routePrefix = $this->currentRoutePrefix;

        $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/upload/' . $model->form_id . '/' . $model->indicator_id . '/' . $model->financial_year_id . '/' . $model->month_range_period_id . '/' . $model->id . '/' . Uuid::uuid4()->toString();

        $this->redirect($route);
    }


    public function actionRules($row): array
    {

        $currentDate = Carbon::now();
        $establishedDate = $this->submissionPeriodRow['date_established'];
        $endDate = $this->submissionPeriodRow['date_ending'];

        $startDate = Carbon::parse($establishedDate);
        $endDate = Carbon::parse($endDate);

        $withinDateRange = $currentDate->between($startDate, $endDate);




        return [


            // Rules for adding data
            Rule::button('add-data')
                ->when(fn() =>  !$withinDateRange)
                ->disable(),

            // Rules for uploading data
            Rule::button('upload')
                ->when(fn($row) => ($row->id && in_array(Form::find($row->id)->name, ['REPORT FORM'])) || !$withinDateRange)
                ->disable(),
        ];
    }
    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
