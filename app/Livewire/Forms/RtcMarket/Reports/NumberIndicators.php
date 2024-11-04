<?php

namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Models\Form;
use App\Models\User;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use Livewire\Attributes\Validate;
use App\Helpers\SubmitAggregateData;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class NumberIndicators extends Component
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



    public $formData = [];

    public $disaggregations = [];
    public $inputs = [];
    public $validationRules = [];
    public $validationAttributes = [];
    public function save()
    {

        $this->validate($this->validationRules, [], $this->validationAttributes);
        $data = [];
        foreach ($this->inputs as $disaggregationId => $value) {
            $disaggregationName = IndicatorDisaggregation::find($disaggregationId)->name;
            $data[$disaggregationName] = $value;
        }

        $user = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;



        // Roles for internal users
        if (($user->hasAnyRole('internal') && $user->hasAnyRole('organiser')) || $user->hasAnyRole('admin')) {
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



            $this->disaggregations = IndicatorDisaggregation::where('indicator_id', $this->selectedIndicator)->get();

            // Initialize input fields dynamically based on disaggregations
            foreach ($this->disaggregations as $disaggregation) {

                $name = strtolower($disaggregation->name);
                if ($name != 'total') {


                    $this->inputs[$disaggregation->id] = null;

                    $this->validationRules["inputs.{$disaggregation->id}"] = 'required|numeric|min:0';

                    // Dynamically set custom validation attribute names
                    $this->validationAttributes["inputs.{$disaggregation->id}"] = $disaggregation->name;
                }


                // You can set initial values to 0 or fetch them if available
            }
            $this->disaggregations = $this->disaggregations->whereIn('id', array_keys($this->inputs));

        }
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.reports.number-indicators');
    }
}
