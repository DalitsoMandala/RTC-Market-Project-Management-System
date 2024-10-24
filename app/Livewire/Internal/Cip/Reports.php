<?php

namespace App\Livewire\Internal\Cip;

use Throwable;
use App\Jobs\Mapper;
use App\Jobs\ReportJob;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use Illuminate\Bus\Batch;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use Livewire\Attributes\Lazy;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Bus;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Cache;
use App\Livewire\Tables\ReportingTable;
use App\Models\IndicatorDisaggregation;
use App\Models\ReportStatus;

class Reports extends Component
{


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
    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->disaggregations = IndicatorDisaggregation::get();
    }

    public function filter()
    {
        // $this->validate();
        $this->filtered = [
            //   'project_id' => $this->selectedProject,
            'indicator' => $this->selectedIndicator === '' ? null : $this->selectedIndicator,
            'reporting_period' => $this->selectedReportingPeriod === '' ? null : $this->selectedReportingPeriod,
            'financial_year' => $this->selectedFinancialYear  === '' ? null : $this->selectedFinancialYear,
            'organisation_id' =>  $this->selectedOrganisation === '' ? null : $this->selectedOrganisation,
            'disaggregation' => $this->selectedDisaggregation === '' ? null : $this->selectedDisaggregation,
            // 'start_date' => $this->starting_period == "" ? null : $this->starting_period,
            //'end_date' => $this->ending_period  == "" ? null : $this->ending_period,
        ];

        $this->dispatch('filtered-data', $this->filtered);

        //$this->dispatch('filtered-data', $this->filtered);
    }

    #[On('reset-filters')]
    public function resetFilters()
    {

        $this->reset(
            'financialYears',
            'reportingPeriod',
            'filtered',
            'selectedOrganisation',
            'selectedDisaggregation',

            'selectedProject',
            'selectedIndicator',
            'selectedReportingPeriod',
            'selectedFinancialYear'
        );

        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->disaggregations = IndicatorDisaggregation::get();

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function load()
    {


        $this->loadingData = true;

        $completed = ReportStatus::where('status', 'completed')->exists();
        $pending = ReportStatus::where('status', 'pending')->exists();
        $processing = ReportStatus::where('status', 'processing')->exists();




        if ($completed) {

            $batch = Bus::batch([
                new ReportJob([])
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
        } else if ($processing) {
            $this->readCache();
        } else {
            $this->loadingData = false;
        }
    }
    public function updated($property, $value)
    {

        if ($property === 'selectedOrganisation') {

            $indicators = ResponsiblePerson::where('organisation_id', $this->selectedOrganisation)->pluck('indicator_id');
            $this->disaggregations = IndicatorDisaggregation::whereIn('indicator_id', $indicators)->get();
            $this->indicators = Indicator::whereIn('id', $indicators)->get();
        }
    }





    public function readCache()
    {

        $this->loadingData = true;
        $check = ReportStatus::where('status', 'completed')->exists();

        if ($check) {
            $this->loadingData = false;
            $this->dispatch('reset-filters');
        }

        $this->progress = ReportStatus::find(1)->progress;
    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
