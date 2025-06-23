<?php

namespace App\Livewire\Internal\Cip;

use App\Helpers\CoreFunctions;
use App\Jobs\Mapper;
use App\Jobs\ReportJob;
use App\Livewire\Tables\ReportingTable;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\ResponsiblePerson;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

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

    // #[Validate('required')]
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

    public $crops = [];
    public $selectedCrop;

    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->disaggregations = IndicatorDisaggregation::get();
        $this->routePrefix = Route::current()->getPrefix();
        $this->crops = CoreFunctions::getCropsWithNull();
        $this->filterOrganisationData();
    }

    public function filterOrganisationData()
    {
        $this->selectedOrganisation = null;
        $this->selectedCrop = '';
        $user = User::find(auth()->user()->id);
        if ($user->hasAnyRole('external')) {
            $this->indicators = Indicator::with('organisation')->whereHas('organisation', function ($query) {
                $query->where('organisations.id', auth()->user()->organisation->id);
            })->get();

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
            'crop' => $this->selectedCrop === '' ? null : $this->selectedCrop,
            // 'start_date' => $this->starting_period == "" ? null : $this->starting_period,
            // 'end_date' => $this->ending_period  == "" ? null : $this->ending_period,
        ];

        $this->dispatch('filtered-data', $this->filtered);

        // $this->dispatch('filtered-data', $this->filtered);
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
            'selectedFinancialYear',
            'selectedCrop'
        );

        $this->projects = Project::get();
        $this->indicators = Indicator::get();
        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->disaggregations = IndicatorDisaggregation::get();
        $this->crops = CoreFunctions::getCropsWithNull();

        $this->resetErrorBag();
        $this->resetValidation();
        $this->filterOrganisationData();
    }

    public function load()
    {
        $this->loadingData = true;

        Artisan::call('update:information');
        $this->readCache();
    }

    public function updated($property, $value)
    {
        if ($property === 'selectedOrganisation') {
            $indicatorIds = ResponsiblePerson::where('organisation_id', $value)->pluck('indicator_id');

            $this->indicators = Indicator::whereIn('id', $indicatorIds)->get();
            $this->disaggregations = IndicatorDisaggregation::whereIn('indicator_id', $indicatorIds)->get()->unique('name');


            $this->selectedDisaggregation = null;
        }

        if ($property === 'selectedIndicator') {
            $this->disaggregations = IndicatorDisaggregation::where('indicator_id', $value)->get()->unique('name');
            $this->selectedDisaggregation = null; // reset only on indicator change
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
