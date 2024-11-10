<?php

namespace App\Livewire\Internal\Cip;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\ReportingPeriod;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Jobs\SendNotificationJob;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Bus;
use App\Models\ReportingPeriodMonth;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\EmployeeBroadcastNotification;

class SubPeriod extends Component
{
    use LivewireAlert;

    public $rowId;
    public $forms = [];

    public $status = true;
    #[Validate('required', as: 'start of submissions')]
    public $start_period;
    #[Validate('required|after:start_period', as: 'end of submissions')]
    public $end_period;
    #[Validate('required', message: 'The form field is required.')]
    public $selectedForm = [];

    public $months = [];
    public $financialYears = [];

    public $projects = [];
    #[Validate('required')]
    public $selectedMonth;
    #[Validate('required', as: 'project year')]
    public $selectedFinancialYear;
    #[Validate('required')]
    public $selectedProject;
    public $expired;
    public $disaggregations = [];
    public $indicators = [];

    #[Validate('required')]
    public $selectedIndicator;

    public $all;

    public $targets = []; // This will hold the dynamically added targets
    public $disableTarget = true;


    protected $rules = [
        'targets.*' => 'required',
        'targets.*.name' => 'required|string|distinct',
        'targets.*.value' => 'required|numeric',
    ];


    protected $messages = [
        'targets.*.name.required' => 'Target name required',
        'targets.*.value.required' => 'Target value required',
        'targets.*.value.numeric' => 'Target value must be numeric',
        'targets.*.name.distinct' => 'Target name must be unique',

    ];

    public function mount()
    {
        $this->loadData();
        //  $this->targets = [['name' => 'Total', 'value' => '']];
    }
    /**
     * Add a new target to the array
     */
    public function addTarget()
    {

        $this->targets->push([
            'name' => null,
            'value' => null
        ]);


    }

    /**
     * Remove a target from the array
     */
    public function removeTarget($index)
    {
        //  unset($this->targets[$index]);
        //  $this->targets = array_values($this->targets); // Reindex the array
        $this->targets = $this->targets->forget($index)->values();
    }
    public function loadData()
    {
        $this->projects = Project::all();
        $this->financialYears = FinancialYear::all();
        $this->months = ReportingPeriodMonth::all();
        $this->indicators = Indicator::all();
        $this->forms = Form::all();
        $this->disaggregations = IndicatorDisaggregation::all();
        $this->fill([
            'targets' => collect([
                [
                    'name' => '',
                    'value' => ''
                ]
            ])
        ]);
    }

    #[On('editData')]
    public function fillData($rowId)
    {

        $this->rowId = $rowId;

        $submissionPeriod = SubmissionPeriod::findOrFail($rowId);
        $this->start_period = Carbon::parse($submissionPeriod->date_established)->format('Y-m-d');
        $this->end_period = Carbon::parse($submissionPeriod->date_ending)->format('Y-m-d');
        $this->status = $submissionPeriod->is_open;
        $this->selectedIndicator = $submissionPeriod->indicator_id;
        $this->selectedForm[] = $submissionPeriod->form_id;
        $this->selectedMonth = $submissionPeriod->month_range_period_id;
        $this->selectedFinancialYear = $submissionPeriod->financial_year_id;




        $form = Form::find($submissionPeriod->form_id);
        if ($form) {
            $project = $form->project;
            $this->selectedProject = $project->id;
            $this->updateProjectRelatedData($project);
        }

        $indicator = Indicator::find($this->selectedIndicator);
        if ($indicator) {
            $formIds = $indicator->forms->pluck('id');
            $this->all = $formIds;
            $this->forms = $formIds->isNotEmpty() ? Form::whereIn('id', $formIds)->get() : collect();


            $this->dispatch('changed-form', data: $formIds->toArray(), forms: $this->forms);
            $this->dispatch('set-targets');
        }
    }




    public function updateProjectRelatedData($project)
    {


        $period = ReportingPeriod::findOrFail($project->reporting_period_id);
        $this->months = ReportingPeriodMonth::where('period_id', $period->id)->get();
        $this->financialYears = FinancialYear::where('project_id', $project->id)->get();
        $this->indicators = Indicator::where('project_id', $project->id)->get();

        $this->dispatch('update-indicator', data: $this->indicators, selected: $this->selectedIndicator);
    }

    public function save()
    {
        try {
            $this->validate();

        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {
            $data = [
                'date_established' => $this->start_period,
                'date_ending' => $this->end_period,
                'is_open' => $this->status,
                'form_id' => $this->selectedForm[0],
                'is_expired' => $this->expired ?? false,
                'month_range_period_id' => $this->selectedMonth,
                'financial_year_id' => $this->selectedFinancialYear,
                'indicator_id' => $this->selectedIndicator,
            ];
            if ($this->rowId) {
                $submissions = Submission::where('period_id', $this->rowId)->count();
                if ($submissions === 0) {


                    SubmissionPeriod::find($this->rowId)->update($data);
                    $checkTargets = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                        ->where('financial_year_id', $this->selectedFinancialYear)
                        ->get();
                    if ($checkTargets->isNotEmpty()) {
                        foreach ($checkTargets as $target) {
                            $target->delete();
                        }
                    }

                    foreach ($this->targets as $target) {
                        SubmissionTarget::create([

                            'financial_year_id' => $this->selectedFinancialYear,
                            'indicator_id' => $this->selectedIndicator,
                            'target_name' => $target['name'],
                            'target_value' => $target['value'],
                        ]);
                    }

                    if ($this->status == false) {

                        $this->dispatch('timeout');
                        session()->flash('success', 'Updated Successfully. You have closed the submission for this form and period.');
                        $this->sendBroadcast($this->selectedIndicator, $this->selectedForm, 'Submissions have been closed for this form and period.');
                        $this->resetData();
                        return;
                    }

                    session()->flash('success', 'Updated Successfully');


                } else {
                    session()->flash('error', 'Cannot update this record because it has submissions.');
                }
            } else {
                // Check if any existing records have the same criteria and are not expired
                $exists = SubmissionPeriod::where('month_range_period_id', $this->selectedMonth)
                    ->where('financial_year_id', $this->selectedFinancialYear)
                    ->where('indicator_id', $this->selectedIndicator)
                    ->whereIn('form_id', $this->selectedForm)
                    ->where('is_expired', false)
                    ->exists();



                if ($exists) {
                    session()->flash('error', 'This record already exists.');

                    return;
                } else {
                    foreach ($this->selectedForm as $formId) {
                        SubmissionPeriod::create(array_merge($data, ['form_id' => $formId]));
                    }


                    $checkTargets = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                        ->where('financial_year_id', $this->selectedFinancialYear)
                        ->get();
                    if ($checkTargets->isNotEmpty()) {
                        foreach ($checkTargets as $target) {
                            $target->delete();
                        }
                    }

                    foreach ($this->targets as $target) {
                        SubmissionTarget::create([

                            'financial_year_id' => $this->selectedFinancialYear,
                            'indicator_id' => $this->selectedIndicator,
                            'target_name' => $target['name'],
                            'target_value' => $target['value'],
                        ]);
                    }
                    session()->flash('success', 'Created Successfully');


                    $this->sendBroadcast($this->selectedIndicator, $this->selectedForm);
                    return redirect()->to(url()->previous());
                }
            }
        } catch (Throwable $th) {

            session()->flash('error', 'Something went wrong');
        }
    }

    public function sendBroadcast($Indicator, $forms, $errorMessage = null)
    {



        $users = User::with('roles')->get();
        $indicatorFound = Indicator::find($Indicator);
        foreach ($users as $user) {


            $organisationId = $user->organisation->id;


            $responsiblePeople = ResponsiblePerson::where('indicator_id', $indicatorFound->id)
                ->where('organisation_id', $organisationId)
                ->first();


            // Check if the organisation has responsible people
            $hasResponsiblePeople = $responsiblePeople !== null;

            // Check if the responsible person has the required form
            $hasFormAccess = $hasResponsiblePeople ? $responsiblePeople->sources->whereIn('form_id', $forms)->isNotEmpty() : false;

            if ($hasFormAccess) {
                $messageContent = "Submissions are now open, please go to the platform to complete your submission before the period ends.";
                $link = env('APP_URL');

                if ($errorMessage) {

                    Bus::chain([
                        new SendNotificationJob($user, $errorMessage, $link, true)
                    ])->dispatch();

                } else {




                    Bus::chain([
                        new SendNotificationJob($user, $messageContent, $link, false)
                    ])->dispatch();


                }




            }
        }
    }



    public function updatedSelectedProject($value)
    {
        $project = Project::find($value);
        if ($project) {
            $this->updateProjectRelatedData($project);
        } else {
            $this->loadData();
        }
    }

    public function updated($property, $value)
    {
        if ($this->selectedProject && $this->selectedIndicator) {
            $indicator = Indicator::find($this->selectedIndicator);
            if ($indicator) {
                $formIds = $indicator->forms->pluck('id');
                $this->all = $formIds;
                $this->forms = $formIds->isNotEmpty() ? Form::whereIn('id', $formIds)->get() : collect();
                $this->dispatch('changed-form', data: $formIds->toArray(), forms: $this->forms);
            }
        }


    }

    public function getTargets()
    {

        $indicator = Indicator::find($this->selectedIndicator);

        if ($indicator) {
            $disaggregations = $indicator->disaggregations()->pluck('name')->toArray();

            $this->disaggregations = $disaggregations;
        }

        $targets = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->get();
        if ($targets->count() > 0) {
            $newTargets = [];

            // Loop through $targets and populate $newTargets
            foreach ($targets as $target) {
                $newTargets[] = [
                    'name' => $target->target_name,
                    'value' => $target->target_value,
                ];
            }

            $this->fill([
                'targets' => collect($newTargets)
            ]);
        }



    }
    public function updatedSelectedIndicator($value)
    {
        if (!$this->rowId) {
            $this->selectedForm = null;
        }
    }

    public function resetData()
    {
        $this->reset();
        $this->loadData();
        $this->resetErrorBag();
        $this->dispatch('update-indicator');
    }
    public function render()
    {

        return view('livewire.internal.cip.sub-period', []);
    }
}
