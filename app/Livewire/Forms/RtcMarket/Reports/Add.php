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
    public $form_name = "REPORT FORM";
    public $reportIndicators = [];
    public $selectedReportIndicator;



    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();
        $this->array = Route::current()->parameters;
        $this->routePrefix = Route::current()->getPrefix();
        $this->reportIndicators = Indicator::with(['forms', 'organisation'])->whereHas('forms', function ($query) {
            $query->where('name', 'REPORT FORM');
        })->whereHas('organisation', function ($query) {
            $query->where('organisations.id', auth()->user()->organisation->id);
        })

            ->get();

        $getFirstIndicator = $this->reportIndicators->first();

        if ($getFirstIndicator) {
            $this->selectedIndicator = $getFirstIndicator->id;
            $this->indicator = $getFirstIndicator;
            $this->array['indicator_id'] = $this->selectedIndicator;
            $this->selectedReportIndicator = $getFirstIndicator->id;
        }
    }

    public function updatedSelectedReportIndicator($value)
    {

        $this->indicator = Indicator::find($value);
        $this->selectedIndicator = $value;
        $this->array['indicator_id'] = $this->selectedIndicator;
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
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }



        return view('livewire.forms.rtc-market.reports.add');
    }
}
