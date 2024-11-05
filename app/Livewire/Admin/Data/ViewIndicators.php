<?php

namespace App\Livewire\Admin\Data;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Services\IndicatorService;
use App\Models\ReportingPeriodMonth;

class ViewIndicators extends Component
{
    public $indicator_name, $indicator_no, $project_name, $indicator_id, $project_id, $component;
    public $projects;

    public $selectedProject;

    public $indicators;
    // #[Validate('required')]
    public $selectedIndicator;

    public $starting_period;
    //  #[Validate('required')]
    public $ending_period;

    public $filtered;

    public $reportingPeriod = [];
    //  #[Validate('required')]
    public $selectedReportingPeriod;
    //   #[Validate('required')]
    public $selectedFinancialYear;
    //#[Validate('required')]
    public $selectedOrganisation;
    //   #[Validate('required')]
    public $selectedDisaggregation;



    public $financialYears = [];
    public $data = [];

    public $progress = 0;
    public bool $loadingData = false;

    public $organisations = [];
    public $disaggregations = [];

    public $routePrefix;
    public function mount(Indicator $id)
    {
        $this->indicator_name = $id->indicator_name;
        $this->indicator_id = $id->id;
        $this->project_id = $id->project->id;
        $this->indicator_no = $id->indicator_no;
        $this->project_name = strtoupper($id->project->name);
        $this->financialYears = FinancialYear::get()->toArray();
        $this->selectedFinancialYear = FinancialYear::first()->toArray();
        $this->reportingPeriod = ReportingPeriodMonth::get()->toArray();
        $this->selectedReportingPeriod = ReportingPeriodMonth::first()->toArray();
        $this->organisations = Organisation::get()->toArray();
        $this->selectedOrganisation = Organisation::first()->toArray();
        $this->reRender();

    }




    #[On('refreshData')]

    public function refreshEvents()
    {

        $this->component = null;
        $this->dispatch('reload');

    }




    #[On('reload')]
    public function reRender()
    {

        $indicatorService = new IndicatorService();
        $this->component = $indicatorService->getComponent($this->indicator_name, $this->project_name);
    }
    public function render()
    {
        return view('livewire.admin.data.view-indicators');
    }
}
