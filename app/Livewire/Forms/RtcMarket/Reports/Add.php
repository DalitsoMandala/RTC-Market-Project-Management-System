<?php

namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;
use App\Traits\ManualDataTrait;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;
    public $openSubmission = false;
    public $enterprise;

    public $period;

    public $forms = [];

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $submissionPeriodId;
    public $selectedIndicator;
    public $indicatorName;

    public $inputs = [];

    public $formData = [];
    public $targetSet = false;
    public $targetIds = [];

    public $indicator, $array;

    // public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    // {

    //     if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

    //         abort(404);

    //     }

    //     $findForm = Form::find($form_id);
    //     $findIndicator = Indicator::find($indicator_id);
    //     $findFinancialYear = FinancialYear::find($financial_year_id);
    //     $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
    //     $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
    //     if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

    //         abort(404);

    //     } else {
    //         $this->selectedForm = $findForm->id;
    //         $this->selectedIndicator = $findIndicator->id;
    //         $this->selectedFinancialYear = $findFinancialYear->id;
    //         $this->selectedMonth = $findMonthPeriod->id;
    //         $this->submissionPeriodId = $findSubmissionPeriod->id;
    //         //check submission period

    //         $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
    //             ->where('indicator_id', $this->selectedIndicator)
    //             ->where('financial_year_id', $this->selectedFinancialYear)
    //             ->where('month_range_period_id', $this->selectedMonth)
    //             ->where('is_open', true)
    //             ->first();
    //         $target = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
    //             ->where('financial_year_id', $this->selectedFinancialYear)

    //             ->get();
    //         $user = User::find(auth()->user()->id);

    //         $targets = $target->pluck('id');
    //         $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)
    //             ->whereHas('submissionTarget', function ($query) use ($targets) {
    //                 $query->whereIn('submission_target_id', $targets);
    //             })
    //             ->get();

    //         $this->targetIds = $target->pluck('id')->toArray();
    //         $this->indicator = $findIndicator;
    //         $this->array = Route::current()->parameters;



    //         if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

    //             $this->openSubmission = true;
    //             $this->targetSet = true;
    //         } else {
    //             $this->openSubmission = false;
    //             $this->targetSet = false;
    //         }
    //     }

    // }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();
        $this->indicator = Indicator::find($this->selectedIndicator);

        $this->array = Route::current()->parameters;
    }


    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.reports.add');
    }
}
