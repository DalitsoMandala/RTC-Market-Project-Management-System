<?php

namespace App\Livewire\Internal\Cip;

use Throwable;
use App\Jobs\Mapper;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use Illuminate\Bus\Batch;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Bus;
use App\Models\ReportingPeriodMonth;

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
    public $data = [];
    public bool $loadingData = true;
    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->load();

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



    public function load()
    {
        $this->loadingData = true;
        $batch = Bus::batch([
            new Mapper()
        ])->before(function (Batch $batch) {
            // The batch has been created but no jobs have been added...

        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...

        })

            ->dispatch();

    }


    public function readCache()
    {

        $this->loadingData = true;
        $data = cache()->get('report_', []);

        if (!empty($data)) {

            $this->data = $data;
            $this->loadingData = false;

        }

    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}