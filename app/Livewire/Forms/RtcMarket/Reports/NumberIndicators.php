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
use App\Traits\reportDefaultValuesTrait;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class NumberIndicators extends Component
{
    use LivewireAlert;
    public $form_id;
    public $indicator_id;
    public $financial_year_id;
    public $month_period_id;
    public $submission_period_id;
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

    public function mount()
    {
        $this->submissionPeriodId = $this->submission_period_id;
        $this->selectedForm = $this->form_id;
        $this->selectedIndicator = $this->indicator_id;
        $this->selectedFinancialYear = $this->financial_year_id;
        $this->disaggregations = [];
        $this->inputs = [];
        $this->validationRules = [];
        $this->validationAttributes = [];
        $disaggregations = IndicatorDisaggregation::where('indicator_id', $this->selectedIndicator)->get();

        // Count how many disaggregations are available
        $disaggregationCount = $disaggregations->count();

        foreach ($disaggregations as $disaggregation) {
            $name = strtolower($disaggregation->name);

            // Skip 'total' unless it's the only disaggregation
            if ($name === 'total' && $disaggregationCount > 1) {
                continue;
            }

            $this->inputs[$disaggregation->id] = null;
            $this->validationRules["inputs.{$disaggregation->id}"] = 'required|numeric|min:0';
            $this->validationAttributes["inputs.{$disaggregation->id}"] = $disaggregation->name;
        }

        // Filter disaggregations to only those included in inputs
        $this->disaggregations = $disaggregations->whereIn('id', array_keys($this->inputs));
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.reports.number-indicators');
    }
}
