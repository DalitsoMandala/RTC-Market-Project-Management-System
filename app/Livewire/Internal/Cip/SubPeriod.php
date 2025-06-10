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
use App\Models\MailingList;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Models\ReportingPeriod;
use Illuminate\Validation\Rule;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Jobs\SendNotificationJob;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Bus;
use App\Models\ReportingPeriodMonth;
use App\Models\IndicatorDisaggregation;
use App\Jobs\sendAllIndicatorNotificationJob;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\EmployeeBroadcastNotification;
use App\Traits\IndicatorsTrait;

class SubPeriod extends Component
{
    use LivewireAlert;
    use IndicatorsTrait;
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
    public $organisations = [];
    public $selectedOrganisation;
    public $selectedOrganisations = [];

    #[Validate('required')]
    public $selectedIndicator;

    public $all;
    public $targets = [];  // This will hold the dynamically added targets
    public $cip_targets = [];  // This will hold the dynamically added
    public $disableTarget = true;
    public $isCipTargets = false;
    protected $rules = [];
    public $selectAllIndicators = true;
    public $skipTargets = true;

    protected function rules()
    {
        $isCipTargets = $this->isCipTargets;
        return [
            //     'targets.*' => 'required',
            //   'targets.*.name' => 'required|string|distinct',
            //  'targets.*.value' => 'required|numeric',
            // 'cip_targets.*.value' => [
            //     Rule::requiredIf(function () use ($isCipTargets) {
            //         return $isCipTargets === true;  // or whatever condition checks your flag
            //     }),
            // ],
            'selectedOrganisations' => 'required',
        ];
    }

    protected $messages = [
        // 'targets.*.name.required' => 'Target name required',
        // 'targets.*.value.required' => 'Target value required',
        // 'targets.*.value.numeric' => 'Target value must be numeric',
        // 'targets.*.name.distinct' => 'Target name must be unique',
        // 'cip_targets.*.value.required' => 'Target value required',
        // 'cip_targets.*.value.numeric' => 'Target value must be numeric',
    ];

    public function mount()
    {
        $this->loadData();
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
        $this->cip_targets->push([
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
        $this->targets = array_values($this->targets);  // Reindex the array
        $this->cip_targets = array_values($this->cip_targets);  // Reindex the array
        $this->targets = $this->targets->forget($index)->values();
        $this->cip_targets = $this->cip_targets->forget($index)->values();
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
                    'name' => null,
                    'value' => null
                ]
            ]),
            'selectedProject' => 1,
            'cip_targets' => collect([
                [
                    'name' => null,
                    'value' => null
                ]
            ])
        ]);
        $this->organisations = Organisation::get();
        $this->selectedOrganisations = [0];
    }




    #[On('editData')]
    public function fillData($row)
    {
        $row = (object) $row;
        $this->rowId = $row->rn;




        $this->start_period = Carbon::parse($row->date_established)->format('Y-m-d');
        $this->end_period = Carbon::parse($row->date_ending)->format('Y-m-d');
        $this->status = $row->is_open;
        $this->selectedMonth = $row->month_range_period_id;
        $this->selectedFinancialYear = $row->financial_year_id;
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

            if ($this->selectAllIndicators) {
                $this->validate([

                    'selectedMonth' => 'required',
                    'selectedFinancialYear' => 'required',

                    'start_period' => 'required',
                    'end_period' => 'required',
                ]);
            } else {
                $this->validate();
            }
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {


            if ($this->selectAllIndicators) {

                $periods = [];
                $usersWithData = [];

                $users = User::with('organisation', 'organisation.indicatorResponsiblePeople')->has('organisation.indicatorResponsiblePeople')->whereHas('roles', function ($query) {
                    return $query->whereNotIn('name', ['admin', 'project_manager']);
                })->get();

                foreach ($users as $user) {

                    $indicators = $user->organisation->indicatorResponsiblePeople->map(function ($indicator) {

                        return Indicator::find($indicator->indicator_id);
                    })->flatten();
                    $usersWithData[] = [
                        'user' => $user,

                    ];

                    foreach ($indicators as $indicator) {
                        $forms = $indicator->forms;
                        foreach ($forms as $form) {
                            $submissionPeriod = SubmissionPeriod::where('indicator_id', $indicator->id)
                                ->where('form_id', $form->id)
                                ->where('month_range_period_id', $this->selectedMonth)
                                ->where('financial_year_id', $this->selectedFinancialYear)
                                ->first();

                            if (!$submissionPeriod) {
                                $submissionPeriod = new SubmissionPeriod();
                                $submissionPeriod->indicator_id = $indicator->id;
                                $submissionPeriod->form_id = $form->id;
                                $submissionPeriod->month_range_period_id = $this->selectedMonth;
                                $submissionPeriod->financial_year_id = $this->selectedFinancialYear;
                                $submissionPeriod->date_established = $this->start_period;
                                $submissionPeriod->date_ending = $this->end_period;
                                $submissionPeriod->is_open = true;
                                $submissionPeriod->save();
                                $periods[] = $submissionPeriod->id;
                            } else {
                                continue;
                            }
                        }
                        $indicatorIds[] = $indicator->id;
                    }


                    if (count($periods) > 0) {

                        Bus::chain([
                            new sendAllIndicatorNotificationJob($user,  $indicators)
                        ])->dispatch();
                    }
                }


                if (count($periods) > 0) {
                    $this->setMailingList($periods, $usersWithData);
                }

                if (count($periods) === 0) {
                    session()->flash('success', 'Existing submission periods exists for some of the indicators which are still open.');
                    $this->redirect(url()->previous());
                    return;
                }


                session()->flash('success', 'Successfully added submission periods.');
                $this->redirect(url()->previous());
                return;
            }
            $data = $this->prepareSubmissionData();

            if ($this->rowId) {
                $this->handleUpdate($data);
            } else {
                $this->handleCreate($data);
            }
        } catch (Throwable $th) {
            //  dd($th);
            session()->flash('error', 'Something went wrong.');
        }
    }
    public function setMailingList($selectedPeriods, $usersWithData)
    {

        foreach ($selectedPeriods as $period) {

            foreach ($usersWithData as $userData) {
                $user = $userData['user'];

                if (!$user) continue;

                MailingList::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'submission_period_id' => $period
                    ],
                    [
                        'user_id' => $user->id,
                        'submission_period_id' => $period
                    ]

                );
            }
        }
    }
    private function prepareSubmissionData(): array
    {

        if (Carbon::parse($this->end_period)->format('H:i:s') === '00:00:00') {
            $this->end_period = Carbon::parse($this->end_period)
                ->setTime(23, 59, 0)  // Sets to 11:59:00 PM
                ->format('Y-m-d H:i:s'); // Convert back to string if needed
        }
        return [
            'date_established' => $this->start_period,
            'date_ending' => $this->end_period,
            'is_open' => $this->status,
            'form_id' => $this->selectedForm[0],
            'is_expired' => $this->expired ?? false,
            'month_range_period_id' => $this->selectedMonth,
            'financial_year_id' => $this->selectedFinancialYear,
            'indicator_id' => $this->selectedIndicator,
        ];
    }

    private function handleUpdate(array $data): void
    {
        $submissions = Submission::where('period_id', $this->rowId)->count();

        if ($submissions === 0) {
            SubmissionPeriod::find($this->rowId)->update($data);
            $this->updateTargets();

            if ($this->status == false) {
                $this->handleClosedSubmission();
            } else {
                $this->dispatch('timeout');
                session()->flash('success', 'Updated Successfully');
                $this->sendBroadcast($this->selectedIndicator, $this->selectedForm, null, []);
            }
        } else {
            $this->dispatch('timeout');
            session()->flash('error', 'Cannot update this record because it has submissions.');
        }
    }

    private function handleCreate(array $data)
    {
        $exists = SubmissionPeriod::where('month_range_period_id', $this->selectedMonth)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->where('indicator_id', $this->selectedIndicator)
            ->whereIn('form_id', $this->selectedForm)
            ->where('is_expired', false)
            ->where('is_open', true)
            ->exists();
        $selectedPeriods = [];

        if ($exists) {
            $this->dispatch('timeout');
            session()->flash('error', 'This record already exists.');
        } else {
            try {
                foreach ($this->selectedForm as $formId) {
                    $submissionPeriod = SubmissionPeriod::create(array_merge($data, ['form_id' => $formId]));
                    $selectedPeriods[] = $submissionPeriod->id;
                }

                $this->updateTargets();
                session()->flash('success', 'Created Successfully');
                $this->sendBroadcast($this->selectedIndicator, $this->selectedForm, null, $selectedPeriods);
                return redirect()->to(url()->previous());
            } catch (\Throwable $e) {
                throw $e;
            }
        }
    }

    private function updateTargets(): void
    {
        $user = User::find(auth()->user()->id);
        $organisationId = $user->organisation->id;
        if ($this->skipTargets === false) {


            foreach ($this->targets as $key => $target) {
                $subTarget = SubmissionTarget::updateOrCreate(
                    [
                        'financial_year_id' => $this->selectedFinancialYear,
                        'indicator_id' => $this->selectedIndicator,
                        'target_name' => $target['name'],
                    ],
                    [
                        'target_value' => $target['value'],
                    ]
                );

                OrganisationTarget::updateOrCreate(
                    [
                        'submission_target_id' => $subTarget->id,
                        'organisation_id' => $organisationId,
                    ],
                    [
                        'value' => $this->cip_targets[$key]['value'],
                    ]
                );
            }
        }
    }

    private function handleClosedSubmission()
    {
        $form = Form::find($this->selectedForm[0]);
        $period = ReportingPeriodMonth::find($this->selectedMonth);
        session()->flash('success', 'Updated Successfully. You have closed the submission for this form and period.');
        $this->sendBroadcast($this->selectedIndicator, $this->selectedForm, "Unfortunately, submissions have been closed for {$form->name} for the period of ({$period->start_month} - {$period->end_month}).");
        return redirect()->to(url()->previous());
    }


    public function filterUsers($user, $Indicator, $forms, $errorMessage = null)
    {
        $link = match (true) {
            $user->hasAnyRole('manager') => route('cip-submission-period'),
            $user->hasAnyRole('staff') => route('cip-staff-submission-period'),
            default => route('external-submission-period'),
        };

        if ($errorMessage) {
            Bus::chain([
                new SendNotificationJob($user, $errorMessage, $link, true)
            ])->dispatch();
            return;
        }

        $formNames = Form::whereIn('id', $forms)->pluck('name')->toArray();

        $htmlForms = '<br><ol>';
        foreach ($formNames as $formName) {
            $htmlForms .= "<li><b>{$formName}</b></li>";
        }
        $htmlForms = "</br><ol>";
        foreach ($formNames as $formName) {
            $htmlForms .= "<li>
                        <b>{$formName}</b>
                        </li>";
        }
        $indicatorName = Indicator::find($Indicator)->indicator_name;
        $htmlForms .= "</ol></br>";
        $messageContent = "";
        $messageContent .= "<p> Submissions are now open for <b>" . $indicatorName . "</b></p>";
        $messageContent .= $htmlForms;
        $messageContent .= "<p>These forms will be closed on <b>" . Carbon::parse($this->end_period)->format('d/m/Y H:i:A') . "</b>. Please go to the platform to complete your submission before the period ends.</p>";
        $messageContent .= "<p>For more details, please go to the platform.</p>";



        Bus::chain([
            new SendNotificationJob($user, $messageContent, $link, false)
        ])->dispatch();
    }



    public function sendBroadcast($Indicator, $forms, $errorMessage = null, $selectedPeriods = [])
    {
        // Base query for organisations with indicator responsible people
        $organisations = Organisation::query()
            ->with(['indicatorResponsiblePeople', 'users'])
            ->whereHas(
                'indicatorResponsiblePeople',
                fn($query) =>
                $query->where('indicator_id', $this->selectedIndicator)
            );

        // Apply organisation filter if specific one is selected
        if ($this->selectedOrganisations !== [0]) {
            $organisations->whereIn('id', $this->selectedOrganisations);
        }

        // Process each organisation
        $organisations->get()->each(function ($organisation) use ($errorMessage, $selectedPeriods) {
            $organisation->users->each(function ($user) use ($errorMessage, $selectedPeriods) {
                // Skip users with admin/manager roles
                if ($user->hasAnyRole(['admin', 'project_manager'])) {
                    return;
                }

                // Filter users and add to mailing list
                $this->filterUsers(
                    user: $user,
                    Indicator: $this->selectedIndicator,
                    forms: $this->selectedForm,
                    errorMessage: $errorMessage
                );


                // Add user to mailing list for selected periods
                foreach ($selectedPeriods as $period) {
                    MailingList::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'submission_period_id' => $period
                        ],
                        [
                            'user_id' => $user->id,
                            'submission_period_id' => $period
                        ]
                    );
                }
            });
        });
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
                $organisationIds = ResponsiblePerson::where('indicator_id', $indicator->id)->pluck('organisation_id')->toArray();
                $this->organisations = Organisation::whereIn('id', $organisationIds)->get();

                $this->dispatch('changed-form', data: $formIds->toArray(), forms: $this->forms);
            }
        }
    }

    public function getTargets()
    {
        $indicator = Indicator::find($this->selectedIndicator);
        $user = User::find(auth()->user()->id);
        $organisationId = $user->organisation->id;
        $responsiblePeople = $indicator->responsiblePeopleforIndicators->where('organisation_id', $organisationId)->first();
        $this->isCipTargets = $responsiblePeople ? true : false;
        $newTargets = [];
        $newCipTargets = [];
        if ($indicator) {
            $disaggregations = $indicator->disaggregations()->pluck('name')->toArray();

            $this->disaggregations = $disaggregations;
        }

        $targets = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->get();
        if ($targets->count() > 0) {
            // Loop through $targets and populate $newTargets
            foreach ($targets as $target) {
                $newTargets[] = [
                    'name' => $target->target_name,
                    'value' => $target->target_value,
                ];

                $CipTargets = OrganisationTarget::where('submission_target_id', $target->id)
                    ->where('organisation_id', $organisationId)
                    ->get();
                foreach ($CipTargets as $cipTarget) {
                    $newCipTargets[] = [
                        'value' => $cipTarget->value,
                    ];
                }
            }
        } else {
            $newTargets = [
                [
                    'name' => null,
                    'value' => null,
                ]
            ];
            $newCipTargets = [
                [
                    'value' => null,
                ]
            ];
        }

        $this->fill([
            'targets' => collect($newTargets),
            'cip_targets' => collect($newCipTargets),
        ]);
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
