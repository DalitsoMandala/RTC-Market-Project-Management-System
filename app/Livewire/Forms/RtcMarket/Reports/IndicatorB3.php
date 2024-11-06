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

class IndicatorB3 extends Component
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
    // Form data
    public $total_percentage = 0;
    public $volume;
    public $financial_value = 0;
    public $formal_cassava = null;
    public $formal_potato = null;
    public $formal_sweet_potato = null;
    public $formal_imports = null; // Added 'Formal imports' field

    // Readonly fields
    public $annual_value = 0; // Predefined or calculated value
    public $baseline = 0; // Predefined or calculated baseline
    public $yearNumber = 1;

    protected $rules = [
        // Validation rules
        //   /  'total_percentage' => 'required|numeric|min:0|max:100',
        'volume' => 'required|numeric',
        //   'financial_value' => 'required|numeric',
        'formal_cassava' => 'required|numeric',
        'formal_potato' => 'required|numeric',
        'formal_sweet_potato' => 'required|numeric',
        //  'formal_imports' => 'required|numeric',
    ];

    protected $validationAttributes = [
        'total_percentage' => 'Total (% Percentage)',
        'volume' => 'Volume (Metric Tonnes)',
        'financial_value' => 'Financial Value ($)',
        'formal_cassava' => '(Formal) Cassava',
        'formal_potato' => '(Formal) Potato',
        'formal_sweet_potato' => '(Formal) Sweet Potato',
        'formal_imports' => 'Formal Imports',
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

                $this->baseline = $findIndicator->baseline->baseline_value;
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
            'Volume(Metric Tonnes)' => $this->volume,
            'Financial value ($)' => $this->financial_value,
            '(Formal) Cassava' => $this->formal_cassava,
            '(Formal) Potato' => $this->formal_potato,
            '(Formal) Sweet potato' => $this->formal_sweet_potato,
            'Formal imports' => $this->formal_imports,
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
        return view('livewire.forms.rtc-market.reports.indicator-b3');
    }
}
