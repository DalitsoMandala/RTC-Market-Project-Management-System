<?php

namespace App\Livewire\Internal\Cip;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Reports extends Component
{


    public $projects;
    #[Validate('required')]
    public $selectedProject;

    public $indicators;
    // #[Validate('required')]
    public $selectedIndicators = [];

    public $starting_period;
    //  #[Validate('required')]
    public $ending_period;

    public $filtered;

    public $reportingPeriod = [];
    #[Validate('required')]
    public $selectedReportingPeriod;
    #[Validate('required')]
    public $selectedFinancialYear;

    public $financialYears = [];
    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();


    }

    public function filter()
    {
        $this->validate();
        $this->filtered = [
            'project_id' => $this->selectedProject,
            'indicators' => $this->selectedIndicators,
            'reporting_period' => $this->selectedReportingPeriod,
            'financial_year' => $this->selectedFinancialYear,
            // 'start_date' => $this->starting_period == "" ? null : $this->starting_period,
            //'end_date' => $this->ending_period  == "" ? null : $this->ending_period,
        ];



        $this->dispatch('filtered-data', $this->filtered);
    }

    #[On('reset-filters')]
    public function resetFilters()
    {
        $this->selectedProject = null;
        $this->selectedIndicators = [];
        $this->selectedReportingPeriod = null;
        $this->selectedFinancialYear = null;
        $this->reset('financialYears', 'reportingPeriod');


    }
    public function updated($property, $value)
    {

        if ($property === 'selectedProject') {

            $project = Project::find($this->selectedProject);
            $periods = $project->reportingPeriod;
            $reporting_months = ReportingPeriodMonth::whereIn('period_id', $periods)->get();
            $this->reportingPeriod = $reporting_months;
            $financialyears = FinancialYear::where('project_id', $project->id)->get();
            $this->financialYears = $financialyears;

        }

    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
