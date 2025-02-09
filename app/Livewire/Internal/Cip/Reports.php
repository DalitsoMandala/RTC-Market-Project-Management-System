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
use App\Models\ReportStatus;
use App\Models\FinancialYear;
use Livewire\Attributes\Lazy;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Bus;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Livewire\Tables\ReportingTable;
use App\Models\IndicatorDisaggregation;
use Illuminate\Support\Facades\Artisan;

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

    public $routePrefix;
    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->disaggregations = IndicatorDisaggregation::get();
        $this->routePrefix = Route::current()->getPrefix();
    }

    public function filter()
    {
        // $this->validate();
        $this->filtered = [
            //   'project_id' => $this->selectedProject,
            'indicator' => $this->selectedIndicator === '' ? null : $this->selectedIndicator,
            'reporting_period' => $this->selectedReportingPeriod === '' ? null : $this->selectedReportingPeriod,
            'financial_year' => $this->selectedFinancialYear === '' ? null : $this->selectedFinancialYear,
            'organisation_id' => $this->selectedOrganisation === '' ? null : $this->selectedOrganisation,
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


        $pending = ReportStatus::where('status', 'pending')->first();
        $completed = ReportStatus::where('status', 'completed')->first();
        $processing = ReportStatus::where('status', 'processing')->first();




        if ($pending || $completed) {

            Artisan::call('update:information');
        } else if ($processing) {
            $this->readCache();
        }
    }
    public function updated($property, $value)
    {

        if ($this->selectedOrganisation) {

            $indicators = ResponsiblePerson::where('organisation_id', $this->selectedOrganisation)->pluck('indicator_id');
            $this->disaggregations = IndicatorDisaggregation::whereIn('indicator_id', $indicators)->get()->unique('name');
            $this->indicators = Indicator::whereIn('id', $indicators)->get();
        }


        if ($this->selectedOrganisation !== null && $this->selectedIndicator) {
            $indicators = ResponsiblePerson::where('organisation_id', $this->selectedOrganisation)->pluck('indicator_id');
            $this->disaggregations = IndicatorDisaggregation::where('indicator_id', $this->selectedIndicator)->get()->unique('name');
        }
    }





    public function readCache()
    {

        $this->loadingData = true;
        $check = ReportStatus::where('status', 'completed')->exists();

        if ($check) {
            $this->loadingData = false;
            $this->dispatch('reset-filters');
            cache()->clear();
        }

        $this->progress = ReportStatus::find(1)->progress;
    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}