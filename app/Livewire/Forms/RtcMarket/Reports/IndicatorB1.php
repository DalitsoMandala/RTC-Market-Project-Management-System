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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserErrorException;
use App\Models\IndicatorDisaggregation;
use App\Traits\reportDefaultValuesTrait;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;

class IndicatorB1 extends Component
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




    public function save()
    {
        $this->validate();

        // Prepare data to save as JSON
        $data = [
            'total_percentage' => $this->total_percentage,
            'volume' => $this->volume,
            'financial_value' => $this->financial_value,
            'formal_exports' => $this->formal_exports,
            'informal_exports' => $this->informal_exports,
            'annual_value' => $this->annual_value,
            'baseline' => $this->baseline,
        ];

        SubmissionReport::create([]);

        session()->flash('message', 'Targets saved successfully.');
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.reports.indicator-b1');
    }
}
