<?php
namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Helpers\SubmitAggregateData;
use App\Models\Indicator;
use App\Models\User;
use App\Traits\NotifyAdmins;
use App\Traits\reportDefaultValuesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

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

    public $months         = [];
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
    public $farmers    = 0;
    public $processors = 0;
    public $traders    = 0;

    public $baseline_farmers    = 0;
    public $baseline_processors = 0;
    public $baseline_traders    = 0;

    public $income          = 0;
    public $rolled_baseline = 0;
    public $total           = 0;

    public $cassava      = 0;
    public $potato       = 0;
    public $sweet_potato = 0;
    protected $rules     = [

        'farmers'             => 'required|numeric',
        'processors'          => 'required|numeric',
        'traders'             => 'required|numeric',
        'baseline_farmers'    => 'required|numeric',
        'baseline_processors' => 'required|numeric',
        'baseline_traders'    => 'required|numeric',
        'income'              => 'required|numeric',
        'rolled_baseline'     => 'required|numeric',
        'total'               => 'required|numeric',
    ];

    protected $validationAttributes = [

        'farmers'             => 'farmers',
        'processors'          => 'processors',
        'traders'             => 'traders',
        'baseline_farmers'    => 'baseline_farmers',
        'baseline_processors' => 'baseline_processors',
        'baseline_traders'    => 'baseline_traders',
        'income'              => 'income',
        'rolled_baseline'     => 'rolled_baseline',
        'total'               => 'total',
    ];

    public function save()
    {
        $this->validate();

        $user   = User::find(Auth::user()->id);
        $submit = new SubmitAggregateData;
        // Roles for internal users

        $disaggregations = $this->getIndicatorDisaggregations(Indicator::where('id', $this->selectedIndicator)->first()->indicator_no);
        $disaggregations->put('Total', $this->total);
        $disaggregations->put('Income ($)', $this->income);
        $disaggregations->put('Farmers', $this->farmers);
        $disaggregations->put('Processors', $this->processors);
        $disaggregations->put('Traders', $this->traders);
        $disaggregations->put('Rolled Baseline', $this->rolled_baseline);
        $disaggregations->put('Baseline Farmers', $this->baseline_farmers);
        $disaggregations->put('Baseline Processors', $this->baseline_processors);
        $disaggregations->put('Baseline Traders', $this->baseline_traders);
        $disaggregations->put('Cassava', $this->cassava);
        $disaggregations->put('Potato', $this->potato);
        $disaggregations->put('Sweet potato', $this->sweet_potato);

        $data = [
            'Farmers'             => $this->farmers,
            'Processors'          => $this->processors,
            'Traders'             => $this->traders,
            'Baseline Farmers'    => $this->baseline_farmers,
            'Baseline Processors' => $this->baseline_processors,
            'Baseline Traders'    => $this->baseline_traders,
            'income'              => $this->income,
            'Rolled Baseline'     => $this->rolled_baseline,
            'Income ($)'          => $this->income,
            'Cassava'             => $this->cassava,
            'Potato'              => $this->potato,
            'Sweet potato'        => $this->sweet_potato,
            'Total'               => $this->total,
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
    protected function getIndicatorDisaggregations($number): Collection
    {
        $indicator = $this->baseIndicator($number);

        if (! $indicator) {
            return collect();
        }

        return $indicator->disaggregations->pluck('name')->mapWithKeys(fn($name) => [$name => 0]);
    }
    protected function baseIndicator($number): ?Indicator
    {
        return Indicator::where('indicator_no', $number)->first();
    }

    public function render()
    {

        return view('livewire.forms.rtc-market.reports.indicator-b2');
    }
}
