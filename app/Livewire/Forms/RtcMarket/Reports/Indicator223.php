<?php

namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Traits\NotifyAdmins;
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

class Indicator223 extends Component
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
    // Readonly fields
    public $annual_value = 0; // Example predefined or calculated value
    public $baseline = null; //  Example predefined or calculated value
    public $yearNumber = 1;
    public $total_percentage = 0; // Calculated in the frontend
    public $cassava;
    public $potato;
    public $sweet_potato;
    public $basic;
    public $certified;
    public $pos; // POs (Producer Organizations)
    public $individual_farmers; // Individual farmers not in POs
    public $large_scale_farmers;
    public $medium_scale_farmers;
    protected $rules = [
        // 'total_percentage' => 'required|numeric|min:0|max:100',
        'cassava' => 'required|numeric|min:0',
        'potato' => 'required|numeric|min:0',
        'sweet_potato' => 'required|numeric|min:0',
        'basic' => 'required|numeric|min:0',
        'certified' => 'required|numeric|min:0',
        'pos' => 'required|numeric|min:0',
        'individual_farmers' => 'required|numeric|min:0',
        'large_scale_farmers' => 'required|numeric|min:0',
        'medium_scale_farmers' => 'required|numeric|min:0',
        //     'annual_value' => 'required|numeric|min:0', // Not directly input but needs validation
    ];

    protected $validationAttributes = [
        // 'total_percentage' => 'Total (% Percentage)',
        'cassava' => 'Cassava',
        'potato' => 'Potato',
        'sweet_potato' => 'Sweet Potato',
        'basic' => 'Basic',
        'certified' => 'Certified',
        'pos' => 'POs (Producer Organizations)',
        'individual_farmers' => 'Individual Farmers Not in POs',
        'large_scale_farmers' => 'Large Scale Farmers',
        'medium_scale_farmers' => 'Medium Scale Farmers',
        //    'annual_value' => 'Annual Value',
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


                $this->yearNumber = $findFinancialYear->number;
            } else {
                $this->openSubmission = false;
            }
        }
    }
    public function save()
    {
        // Validate form data
        $this->validate();

        $user = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;

        // Prepare data array for submission
        $data = [
            'Total(% Percentage)' => $this->total_percentage,
            'Cassava' => $this->cassava,
            'Potato' => $this->potato,
            'Sweet potato' => $this->sweet_potato,
            'Basic' => $this->basic,
            'Certified' => $this->certified,
            'POs (Producer Organizations)' => $this->pos,
            'Individual farmers not in POs' => $this->individual_farmers,
            'Large scale farmers' => $this->large_scale_farmers,
            'Medium scale farmers' => $this->medium_scale_farmers,
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
                route('cip-submissions'),
                'manager'
            );
        }

        // Roles for external users
        else if ($user->hasAnyRole('external') || $user->hasAnyRole('staff')) {


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
        return view('livewire.forms.rtc-market.reports.indicator223');
    }
}
