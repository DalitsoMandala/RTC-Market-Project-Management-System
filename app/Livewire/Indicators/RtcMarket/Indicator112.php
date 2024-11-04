<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Models\Indicator;

use App\Helpers\rtc_market\indicators\A1;

use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\SystemReport;
use App\Models\SystemReportData;

class Indicator112 extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $rowId;
    public $data = [];
    public $indicator_no;
    public $indicator_id, $project_id;
    public $indicator_name;

    public $total;

    public $selectedProjectYear = [];

    public $projectYears = [];

    public $selectedOrganisation = 1;

    public $reportingPeriods = [];

    public $reporting_period;
    public $financial_year;





    public function calculations()
    {
        $reportId = SystemReport::where('indicator_id', $this->indicator_id)
            ->where('reporting_period_id', $this->reporting_period['id'])
            ->where('project_id', $this->project_id)
            ->where('organisation_id', auth()->user()->organisation->id)
            ->where('financial_year_id', $this->financial_year['id'])
            ->first();


        if ($reportId) {
            $data = SystemReportData::where('system_report_id', $reportId->id)->pluck('value', 'name')->toArray();
            $this->data = $data;
            $this->total = $this->data['Total'];
        }


    }
    public function mount()
    {
        $this->calculations();

    }

    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator112');
    }
}
