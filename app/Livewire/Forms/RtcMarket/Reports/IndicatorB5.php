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
use App\Traits\reportDefaultValuesTrait;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;

class IndicatorB5 extends Component
{
    use LivewireAlert;
    use NotifyAdmins;
    use reportDefaultValuesTrait;
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
    public $annual_value = 0;
    public $baseline = null;
    public $cassava = null;
    public $potato = null;
    public $sweet_potato = null;
    public $produce = null;
    public $seed = null;
    public $cuttings = null;
    // Readonly fields
    public $yearNumber = 1;

    protected $rules = [
        'total_percentage' => 'required|numeric|min:0|max:100',
        'cassava' => 'required|numeric',
        'potato' => 'required|numeric',
        'sweet_potato' => 'required|numeric',
        'produce' => 'required|numeric',
        'seed' => 'required|numeric',
        'cuttings' => 'required|numeric',
        'baseline' => 'required|numeric',
    ];

    protected $validationAttributes = [
        'total_percentage' => 'Total (% Percentage)',
        'cassava' => 'Cassava',
        'potato' => 'Potato',
        'sweet_potato' => 'Sweet Potato',
        'produce' => 'Produce',
        'seed' => 'Seed',
        'cuttings' => 'Cuttings',
        'baseline' => 'Previous value',
    ];



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
            'Seed' => $this->seed,
            'Cuttings' => $this->cuttings,
            'Produce' => $this->produce,
            'Annual value' => $this->annual_value,
            'Baseline' => $this->baseline,
        ];

        $this->notifyAdminsAndManagers();

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
        } else if ($user->hasAnyRole('external')) {


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
        return view('livewire.forms.rtc-market.reports.indicator-b5');
    }
}
