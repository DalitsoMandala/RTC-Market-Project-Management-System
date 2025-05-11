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
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubmitAggregateData;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserErrorException;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;
use App\Traits\NotifyAdmins;

class Indicator231 extends Component
{
    use LivewireAlert;
    use NotifyAdmins;
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

    // Form data
    public $total_percentage = 0; // Calculated in the frontend
    public $cassava;
    public $potato;
    public $sweet_potato;
    public $pos; // POs (Producer Organizations)
    public $smes; // SMEs
    public $large_scale_commercial_farmers;
    public $basic; // Basic
    public $certified; // Certified
    public $annual_value = 0; // Predefined or calculated value
    public $baseline = null; // Predefined or calculated baseline
    public $yearNumber = 1;
    // Validation rules
    protected $rules = [
        //  'total_percentage' => 'required|numeric|min:0|max:100',
        'cassava' => 'required|numeric|min:0',
        'potato' => 'required|numeric|min:0',
        'sweet_potato' => 'required|numeric|min:0',
        'pos' => 'required|numeric|min:0',
        'smes' => 'required|numeric|min:0',
        'large_scale_commercial_farmers' => 'required|numeric|min:0',
        'basic' => 'required|numeric|min:0',
        'certified' => 'required|numeric|min:0',
        //   'annual_value' => 'required|numeric|min:0', // Not directly input but needs validation
        'baseline' => 'required|numeric',
    ];

    protected $validationAttributes = [
        //'total_percentage' => 'Total (% Percentage)',
        'cassava' => 'Cassava',
        'potato' => 'Potato',
        'sweet_potato' => 'Sweet Potato',
        'pos' => 'POs (Producer Organizations)',
        'smes' => 'SMEs',
        'large_scale_commercial_farmers' => 'Large Scale Commercial Farmers',
        //   'annual_value' => 'Annual Value',
        'baseline' => 'Previous value',
    ];

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);
        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);
        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();
            $this->indicatorName = $findIndicator->indicator_name;
            if ($submissionPeriod) {

                $this->openSubmission = true;

                $this->baseline = $findFinancialYear->number == 1 ? $findIndicator->baseline->baseline_value : null;

                $this->yearNumber = $findFinancialYear->number;
            } else {
                $this->openSubmission = false;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $user = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;

        // Prepare data array for submission
        $data = [
            'Total (% Percentage)' => $this->total_percentage,
            'Cassava' => $this->cassava,
            'Potato' => $this->potato,
            'Sweet potato' => $this->sweet_potato,
            'POs (Producer Organizations)' => $this->pos,
            'SMEs' => $this->smes,
            'Large scale commercial farmers' => $this->large_scale_commercial_farmers,
            'Basic' => $this->basic,
            'Certified' => $this->certified,
            'Annual value' => $this->annual_value,
            'Baseline' => $this->baseline,
        ];

        $this->notifyAdminsAndManagers();

        // Roles for internal users
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $submit->submit_aggregate_data(
                $data,
                $user,
                $this->submissionPeriodId,
                $this->selectedForm,
                $this->selectedIndicator,
                $this->selectedFinancialYear,
                $user->hasAnyRole('admin') ? route('admin-submissions') : route('cip-submissions'),
                'manager'
            );
        }
        // Roles for external users
        // Roles for external users
        else if ($user->hasAnyRole('external')) {


            $submit->submit_aggregate_data(
                $data,
                $user,
                $this->submissionPeriodId,
                $this->selectedForm,
                $this->selectedIndicator,
                $this->selectedFinancialYear,
                route('external-submissions'),
                'external'
            );
        } else if ($user->hasAnyRole('staff')) {


            $submit->submit_aggregate_data(
                $data,
                $user,
                $this->submissionPeriodId,
                $this->selectedForm,
                $this->selectedIndicator,
                $this->selectedFinancialYear,
                route('cip-staff-submissions'),
                'staff'
            );
        }
    }
    public function render()
    {
        return view('livewire.forms.rtc-market.reports.indicator231');
    }
}
