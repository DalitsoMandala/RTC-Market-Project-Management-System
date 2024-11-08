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

class IndicatorB6 extends Component
{
    use LivewireAlert;
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

    public $annual_value = 0; // Predefined or calculated value
    public $baseline = null; // Predefined or calculated baseline
    public $yearNumber = 1;
    // Form data
    public $total_percentage = 0; // Calculated in the frontend
    public $cassava = 0; // User input
    public $potato = 0; // User input
    public $sweet_potato = 0; // User input   public $baseline = null; // Read-only, predefined or calculated value

    // Validation rules
    protected $rules = [

        'cassava' => 'required|numeric|min:0',
        'potato' => 'required|numeric|min:0',
        'sweet_potato' => 'required|numeric|min:0',

    ];

    protected $validationAttributes = [
        'total_percentage' => 'Total (% Percentage)',
        'cassava' => 'Cassava',
        'potato' => 'Potato',
        'sweet_potato' => 'Sweet Potato',
        'annual_value' => 'Annual Value',
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
        $this->validate();

        $user = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;

        $data = [
            'Total(% Percentage)' => $this->total_percentage,
            'Cassava' => $this->cassava,
            'Potato' => $this->potato,
            'Sweet potato' => $this->sweet_potato,
            'Annual value' => $this->annual_value,
            'Baseline' => $this->baseline,
        ];

        // Roles for internal users
        if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {
            $submit->submit_aggregate_data(
                $data,
                $user,
                $this->submissionPeriodId,
                $this->selectedForm,
                $this->selectedIndicator,
                $this->selectedFinancialYear,
                route('cip-internal-submissions'),
                'internal'
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

        }

    }
    public function render()
    {
        return view('livewire.forms.rtc-market.reports.indicator-b6');
    }
}
