<?php

namespace App\Livewire\Internal\Cip;

use App\Jobs\sendAllIndicatorNotificationJob;
use App\Jobs\SendNotificationJob;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\MailingList;
use App\Models\Organisation;
use App\Models\OrganisationTarget;
use App\Models\Project;
use App\Models\ReportingPeriod;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\User;
use App\Services\ProjectService;
use App\Traits\IndicatorsTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class SubPeriod extends Component
{
    use LivewireAlert;
    use IndicatorsTrait;

    public $rowId;
    public $rowData = [];
    public $forms = [];

    public bool $is_open = false;
    public bool $is_expired = false;
    public bool $is_restricted = false;

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

    public $disaggregations = [];
    public $indicators = [];
    public $organisations = [];
    public $selectedOrganisation;
    public $selectedOrganisations = [];

    public bool $notifyUsers = true;
    public bool $bypassNotifications = false;

    #[Validate('required')]
    public $selectedIndicator;

    public $all;
    public $targets = [];
    public $cip_targets = [];
    public $disableTarget = true;
    public $isCipTargets = false;

    protected $rules = [];

    public $selectAllIndicators = true;
    public $skipTargets = true;

    protected function rules()
    {
        return [
            'selectedOrganisations' => 'required',
        ];
    }

    protected $messages = [];

    public function mount()
    {
        $this->loadData();

        $this->selectedProject = ProjectService::app_project_id();
    }

    /*
    |--------------------------------------------------------------------------
    | Target management
    |--------------------------------------------------------------------------
    */

    public function addTarget()
    {
        $this->targets->push([
            'name' => null,
            'value' => null,
        ]);

        $this->cip_targets->push([
            'name' => null,
            'value' => null,
        ]);
    }

    public function removeTarget($index)
    {
        $this->targets = collect($this->targets)
            ->forget($index)
            ->values();

        $this->cip_targets = collect($this->cip_targets)
            ->forget($index)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Load component data
    |--------------------------------------------------------------------------
    */

    public function loadData()
    {
        $this->projects = Project::all();
        $this->financialYears = FinancialYear::all();
        $this->months = ReportingPeriodMonth::all();
        $this->indicators = Indicator::where('is_active', true)->get();
        $this->forms = Form::all();
        $this->disaggregations = IndicatorDisaggregation::all();

        $this->fill([
            'targets' => collect([
                [
                    'name' => null,
                    'value' => null,
                ],
            ]),

            'selectedProject' => 1,

            'cip_targets' => collect([
                [
                    'name' => null,
                    'value' => null,
                ],
            ]),
        ]);

        $this->organisations = Organisation::get();
        $this->selectedOrganisations = [0];
    }

    /*
    |--------------------------------------------------------------------------
    | Edit submission period
    |--------------------------------------------------------------------------
    */

    #[On('edit-period')]
    public function fillData($row)
    {
        $row = (object) $row;

        $this->rowData = $row;
        $this->rowId = $row->id;

        $this->start_period = Carbon::parse($row->date_established)
            ->format('Y-m-d H:i:s');

        $this->end_period = Carbon::parse($row->date_ending)
            ->format('Y-m-d H:i:s');

        $this->is_open = (bool) $row->is_open;
        $this->is_expired = (bool) $row->is_expired;
        $this->is_restricted = (bool) $row->is_restricted;

        // Normalize existing expired records.
        if ($this->is_expired) {
            $this->is_open = false;
            $this->is_restricted = false;
        }

        $this->selectedMonth = $row->reporting_period_month_id;
        $this->selectedFinancialYear = $row->financial_year_id;

        $this->bypassNotifications = (bool) $row->bypass_notification;
    }

    /*
    |--------------------------------------------------------------------------
    | Submission status management
    |--------------------------------------------------------------------------
    */

    /**
     * When a period expires, automatically close submissions
     * and remove submission restrictions.
     */
    public function updatedIsExpired($value): void
    {
        $this->is_expired = (bool) $value;

        if ($this->is_expired) {
            $this->is_open = false;
            $this->is_restricted = false;
        }
    }

    /**
     * An expired period cannot be reopened.
     */
    public function updatedIsOpen($value): void
    {
        if ($this->is_expired && $value) {
            $this->is_open = false;
        }
    }

    /**
     * An expired period cannot be restricted.
     */
    public function updatedIsRestricted($value): void
    {
        if ($this->is_expired && $value) {
            $this->is_restricted = false;
        }
    }

    /**
     * Validate the submission period status before updating.
     *
     * A period must either be open or expired.
     * Both statuses cannot be false.
     */
    private function validateSubmissionStatus(): bool
    {
        if (! $this->is_expired && ! $this->is_open) {
            $this->addError(
                'is_open',
                'You cannot close submissions without marking the period as expired. Either keep submissions open or mark the period as expired.'
            );

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Project-related data
    |--------------------------------------------------------------------------
    */

    public function updateProjectRelatedData($project)
    {
        $period = ReportingPeriod::findOrFail(
            $project->reporting_period_id
        );

        $this->months = ReportingPeriodMonth::where(
            'reporting_period_month_id',
            $period->id
        )->get();

        $this->financialYears = FinancialYear::where(
            'project_id',
            $project->id
        )->get();

        $this->indicators = Indicator::where(
            'project_id',
            $project->id
        )
            ->where('is_active', true)
            ->get();

        $this->dispatch(
            'update-indicator',
            data: $this->indicators,
            selected: $this->selectedIndicator
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save submission period
    |--------------------------------------------------------------------------
    */

    public function save()
    {
        $this->resetErrorBag();

        try {
            $this->validate([
                'selectedMonth' => 'required',
                'selectedFinancialYear' => 'required',
                'selectedProject' => 'required',
                'start_period' => 'required',
                'end_period' => 'required',
            ]);
        } catch (ValidationException $e) {
            session()->flash(
                'validation_error',
                'There are errors in the form.'
            );

            throw $e;
        }

        try {

            /*
             * UPDATE EXISTING SUBMISSION PERIOD
             */
            if ($this->rowId) {
                $this->handleUpdate($this->rowId);

                return;
            }

            /*
             * CREATE NEW SUBMISSION PERIOD
             *
             * New periods are always created open and not expired.
             */

            $existingSubmissionPeriod = SubmissionPeriod::where(
                'reporting_period_month_id',
                $this->selectedMonth
            )
                ->where(
                    'financial_year_id',
                    $this->selectedFinancialYear
                )
                ->where('is_open', true)
                ->first();

            if ($existingSubmissionPeriod) {
                session()->flash(
                    'error',
                    'This submission period exists.'
                );

                return $this->redirect(url()->previous());
            }

            $newSubmissionPeriod = new SubmissionPeriod();

            $newSubmissionPeriod->reporting_period_month_id =
                $this->selectedMonth;

            $newSubmissionPeriod->financial_year_id =
                $this->selectedFinancialYear;

            $newSubmissionPeriod->date_established =
                $this->start_period;

            $newSubmissionPeriod->date_ending =
                $this->end_period;

            $newSubmissionPeriod->is_open = true;
            $newSubmissionPeriod->is_expired = false;
            $newSubmissionPeriod->is_restricted = false;

            $newSubmissionPeriod->bypass_notification =
                $this->bypassNotifications;

            $newSubmissionPeriod->save();

            /*
             * Get users who should receive notifications.
             */
            $users = User::with(
                'organisation',
                'organisation.indicatorResponsiblePeople'
            )
                ->where('is_active', true)
                ->has('organisation.indicatorResponsiblePeople')
                ->whereHas('roles', function ($query) {
                    $query->whereNotIn('name', [
                        'admin',
                        'project-manager',
                        'monitor',
                        'enumerator',
                    ]);
                })
                ->get();

            /*
             * Send notifications after creating the period.
             */
            if ($this->notifyUsers) {

                foreach ($users as $user) {

                    $indicatorIds = $user->organisation
                        ->indicatorResponsiblePeople
                        ->pluck('indicator_id');

                    $indicators = Indicator::whereIn(
                        'id',
                        $indicatorIds
                    )
                        ->where('is_active', true)
                        ->get();

                    if ($indicators->isNotEmpty()) {
                        Bus::chain([
                            new sendAllIndicatorNotificationJob(
                                $user,
                                $indicators
                            ),
                        ])->dispatch();
                    }
                }
            }

            session()->flash(
                'success',
                'Successfully added a submission period.'
            );

            return $this->redirect(url()->previous());

        } catch (Throwable $th) {

            Log::error('Failed to save submission period.', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            session()->flash(
                'error',
                'Something went wrong.'
            );

            return;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update submission period
    |--------------------------------------------------------------------------
    */

    private function handleUpdate($rowId): void
    {
        /*
         * First normalize expired periods.
         */
        if ($this->is_expired) {
            $this->is_open = false;
            $this->is_restricted = false;
        }

        /*
         * Enforce the rule:
         *
         * is_open = true, is_expired = false
         * OR
         * is_open = false, is_expired = true
         *
         * Both cannot be false.
         */
        if (! $this->validateSubmissionStatus()) {
            return;
        }

        /*
         * Save changes inside a transaction.
         */
        DB::transaction(function () use ($rowId) {

            $period = SubmissionPeriod::findOrFail($rowId);

            $period->update([
                'is_open' => $this->is_open,
                'is_expired' => $this->is_expired,
                'is_restricted' => $this->is_restricted,

                'date_ending' => $this->end_period,
                'date_established' => $this->start_period,

                'reporting_period_month_id' => $this->selectedMonth,
                'financial_year_id' => $this->selectedFinancialYear,

                'bypass_notification' => $this->bypassNotifications,
            ]);
        });

        session()->flash(
            'success',
            'Submission period updated successfully.'
        );

        $this->resetData();

        $this->dispatch('refresh');
    }

    /*
    |--------------------------------------------------------------------------
    | Mailing list
    |--------------------------------------------------------------------------
    */

    public function setMailingList($selectedPeriods, $usersWithData)
    {
        foreach ($selectedPeriods as $period) {

            foreach ($usersWithData as $userData) {

                $user = $userData['user'];

                if (! $user) {
                    continue;
                }

                MailingList::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'submission_period_new_id' => $period,
                    ],
                    [
                        'user_id' => $user->id,
                        'submission_period_new_id' => $period,
                    ]
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare submission data
    |--------------------------------------------------------------------------
    */

    private function prepareSubmissionData(): array
    {
        if (
            Carbon::parse($this->end_period)->format('H:i:s')
            === '00:00:00'
        ) {
            $this->end_period = Carbon::parse($this->end_period)
                ->setTime(23, 59, 59)
                ->format('Y-m-d H:i:s');
        }

        return [
            'date_established' => $this->start_period,
            'date_ending' => $this->end_period,

            'is_open' => $this->is_open,
            'is_expired' => $this->is_expired,

            'form_id' => $this->selectedForm[0],

            'reporting_period_month_id' => $this->selectedMonth,
            'financial_year_id' => $this->selectedFinancialYear,
            'indicator_id' => $this->selectedIndicator,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Create submission periods
    |--------------------------------------------------------------------------
    */

    private function handleCreate(Collection $data)
    {
        $exists = SubmissionPeriod::where(
            'reporting_period_month_id',
            $this->selectedMonth
        )
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->where('indicator_id', $this->selectedIndicator)
            ->whereIn('form_id', $this->selectedForm)
            ->where('date_established', $this->start_period)
            ->where('date_ending', $this->end_period)
            ->exists();

        $selectedPeriods = [];

        if ($exists) {
            $this->dispatch('timeout');

            session()->flash(
                'error',
                'This record already exists.'
            );
        } else {

            try {

                foreach ($this->selectedForm as $formId) {

                    $submissionPeriod = SubmissionPeriod::create(
                        array_merge(
                            $data->toArray(),
                            ['form_id' => $formId]
                        )
                    );

                    $selectedPeriods[] = $submissionPeriod->id;
                }

                $this->updateTargets();

                session()->flash(
                    'success',
                    'Created Successfully'
                );

                $this->sendBroadcast(
                    $this->selectedIndicator,
                    $this->selectedForm,
                    null,
                    $selectedPeriods
                );

                return redirect()->to(url()->previous());

            } catch (Throwable $e) {
                throw $e;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update targets
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Closed submission notification
    |--------------------------------------------------------------------------
    */

    private function handleClosedSubmission()
    {
        $form = Form::find($this->selectedForm[0]);

        $period = ReportingPeriodMonth::find($this->selectedMonth);

        session()->flash(
            'success',
            'Updated Successfully. You have closed the submission for this form and period.'
        );

        $this->sendBroadcast(
            $this->selectedIndicator,
            $this->selectedForm,
            "Unfortunately, submissions have been closed for {$form->name} for the period of ({$period->start_month} - {$period->end_month})."
        );

        return redirect()->to(url()->previous());
    }

    /*
    |--------------------------------------------------------------------------
    | User notifications
    |--------------------------------------------------------------------------
    */

    public function filterUsers(
        $user,
        $Indicator,
        $forms,
        $errorMessage = null
    ) {
        $link = match (true) {
            $user->hasAnyRole('manager') => route('cip-submission-period'),
            $user->hasAnyRole('staff') => route('cip-staff-submission-period'),
            default => route('external-submission-period'),
        };

        if ($errorMessage) {

            Bus::chain([
                new SendNotificationJob(
                    $user,
                    $errorMessage,
                    $link,
                    true
                ),
            ])->dispatch();

            return;
        }

        $formNames = Form::whereIn('id', $forms)
            ->pluck('name')
            ->toArray();

        $htmlForms = '<br><ol>';

        foreach ($formNames as $formName) {
            $htmlForms .= "<li><b>{$formName}</b></li>";
        }

        $htmlForms .= '</ol><br>';

        $indicatorName = Indicator::find($Indicator)->indicator_name;

        $messageContent = '';

        $messageContent .=
            '<p>Submissions are now open for <b>'
            . $indicatorName
            . '</b></p>';

        $messageContent .= $htmlForms;

        $messageContent .=
            '<p>These forms will be closed on <b>'
            . Carbon::parse($this->end_period)->format('d/m/Y H:i:A')
            . '</b>. Please go to the platform to complete your submission before the period ends.</p>';

        $messageContent .=
            '<p>For more details, please go to the platform.</p>';

        Bus::chain([
            new SendNotificationJob(
                $user,
                $messageContent,
                $link,
                false
            ),
        ])->dispatch();
    }

    /*
    |--------------------------------------------------------------------------
    | Broadcast notifications
    |--------------------------------------------------------------------------
    */

    public function sendBroadcast(
        $Indicator,
        $forms,
        $errorMessage = null,
        $selectedPeriods = []
    ) {
        $organisations = Organisation::query()
            ->with([
                'indicatorResponsiblePeople',
                'users',
            ])
            ->whereHas(
                'indicatorResponsiblePeople',
                fn ($query) => $query->where(
                    'indicator_id',
                    $this->selectedIndicator
                )
            );

        if ($this->selectedOrganisations !== [0]) {
            $organisations->whereIn(
                'id',
                $this->selectedOrganisations
            );
        }

        $organisations->get()->each(
            function ($organisation) use (
                $errorMessage,
                $selectedPeriods
            ) {
                $organisation->users->each(
                    function ($user) use (
                        $errorMessage,
                        $selectedPeriods
                    ) {

                        if ($user->hasAnyRole([
                            'admin',
                            'project-manager',
                        ])) {
                            return;
                        }

                        $this->filterUsers(
                            user: $user,
                            Indicator: $this->selectedIndicator,
                            forms: $this->selectedForm,
                            errorMessage: $errorMessage
                        );

                        foreach ($selectedPeriods as $period) {

                            MailingList::updateOrCreate(
                                [
                                    'user_id' => $user->id,
                                    'submission_period_new_id' => $period,
                                ],
                                [
                                    'user_id' => $user->id,
                                    'submission_period_new_id' => $period,
                                ]
                            );
                        }
                    }
                );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Livewire update hooks
    |--------------------------------------------------------------------------
    */

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

                $this->forms = $formIds->isNotEmpty()
                    ? Form::whereIn('id', $formIds)->get()
                    : collect();

                $organisationIds = ResponsiblePerson::where(
                    'indicator_id',
                    $indicator->id
                )
                    ->pluck('organisation_id')
                    ->toArray();

                $this->organisations = Organisation::whereIn(
                    'id',
                    $organisationIds
                )->get();

                $this->dispatch(
                    'changed-form',
                    data: $formIds->toArray(),
                    forms: $this->forms
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Indicator targets
    |--------------------------------------------------------------------------
    */

    public function getTargets()
    {
        $indicator = Indicator::find($this->selectedIndicator);

        if (! $indicator) {
            return;
        }

        $user = User::find(auth()->user()->id);

        $organisationId = $user->organisation->id;

        $responsiblePeople = $indicator
            ->responsiblePeopleforIndicators
            ->where('organisation_id', $organisationId)
            ->first();

        $this->isCipTargets = $responsiblePeople ? true : false;

        $newTargets = [];
        $newCipTargets = [];

        $this->disaggregations = $indicator
            ->disaggregations()
            ->pluck('name')
            ->toArray();

        $targets = SubmissionTarget::where(
            'indicator_id',
            $this->selectedIndicator
        )
            ->where(
                'financial_year_id',
                $this->selectedFinancialYear
            )
            ->get();

        if ($targets->count() > 0) {

            foreach ($targets as $target) {

                $newTargets[] = [
                    'name' => $target->target_name,
                    'value' => $target->target_value,
                ];

                $cipTargets = OrganisationTarget::where(
                    'submission_target_id',
                    $target->id
                )
                    ->where(
                        'organisation_id',
                        $organisationId
                    )
                    ->get();

                foreach ($cipTargets as $cipTarget) {

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
                ],
            ];

            $newCipTargets = [
                [
                    'value' => null,
                ],
            ];
        }

        $this->fill([
            'targets' => collect($newTargets),
            'cip_targets' => collect($newCipTargets),
        ]);
    }

    public function updatedSelectedIndicator($value)
    {
        if (! $this->rowId) {
            $this->selectedForm = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reset component
    |--------------------------------------------------------------------------
    */

    public function resetData()
    {
        $this->reset();

        $this->loadData();

        $this->resetErrorBag();

        $this->dispatch('update-indicator');
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.internal.cip.sub-period', []);
    }
}
