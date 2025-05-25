<?php

namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Helpers\SubmitAggregateData;
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
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserErrorException;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;
use App\Traits\ManualDataTrait;
use App\Traits\NotifyAdmins;
use App\Traits\reportDefaultValuesTrait;

class IndicatorB2 extends Component
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

    //form data
    public $total_percentage = 0;
    public $volume;
    public $financial_value = 0;
    public $formal_exports = [
        'cassava' => null,
        'potato' => null,
        'sweet_potato' => null,
    ];
    public $informal_exports = [
        'cassava' => null,
        'potato' => null,
        'sweet_potato' => null,
    ];

    // Readonly fields
    public $annual_value = 0; // Example predefined or calculated value
    public $baseline = null; //  Example predefined or calculated value
    public $yearNumber = 1;

    protected $rules = [

        'volume' => 'required|numeric',
        //  'financial_value' => 'required|numeric',
        'formal_exports.cassava' => 'required|numeric',
        'formal_exports.potato' => 'required|numeric',
        'formal_exports.sweet_potato' => 'required|numeric',
        'informal_exports.cassava' => 'required|numeric',
        'informal_exports.potato' => 'required|numeric',
        'informal_exports.sweet_potato' => 'required|numeric',
        'baseline' => 'required|numeric',
    ];

    protected $validationAttributes = [

        'volume' => 'Volume (Metric Tonnes)',
        // 'financial_value' => 'Financial Value ($)',
        'formal_exports.cassava' => 'Formal Exports - Cassava',
        'formal_exports.potato' => 'Formal Exports - Potato',
        'formal_exports.sweet_potato' => 'Formal Exports - Sweet Potato',
        'informal_exports.cassava' => 'Informal Exports - Cassava',
        'informal_exports.potato' => 'Informal Exports - Potato',
        'informal_exports.sweet_potato' => 'Informal Exports - Sweet Potato',
        'baseline' => 'Previous value',
    ];

    public function save()
    {
        $this->validate();

        $user = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;
        // Roles for internal users
        $formal_cassava = $this->formal_exports['cassava'];
        $formal_potato = $this->formal_exports['potato'];
        $formal_sweet_potato = $this->formal_exports['sweet_potato'];
        $informal_cassava = $this->informal_exports['cassava'];
        $informal_potato = $this->informal_exports['potato'];
        $informal_sweet_potato = $this->informal_exports['sweet_potato'];

        $data = [
            'Total(% Percentage)' => $this->total_percentage,
            'Volume (Metric Tonnes)' => $this->volume,
            'Financial value ($)' => $this->financial_value,
            '(Formal) Cassava' => $formal_cassava,
            '(Formal) Potato' => $formal_potato,
            '(Formal) Sweet potato' => $formal_sweet_potato,
            '(Informal) Cassava' => $informal_cassava,
            '(Informal) Potato' => $informal_potato,
            '(Informal) Sweet potato' => $informal_sweet_potato,
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
        }

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

        return view('livewire.forms.rtc-market.reports.indicator-b2');
    }
}
