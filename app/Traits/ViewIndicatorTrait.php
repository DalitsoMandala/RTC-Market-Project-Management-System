<?php

namespace App\Traits;

use App\Helpers\CoreFunctions;
use App\Models\User;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Services\IndicatorService;
use App\Models\ReportingPeriodMonth;

trait ViewIndicatorTrait
{
    //

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
    public $crops;
    public $selectedCrop;
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
        $indicatorOrganisations = Indicator::with('organisation')->where('id', $this->indicator_id)->get()->pluck('organisation');
        $organisationIds = $indicatorOrganisations->first()->pluck('id');
        $this->organisations = Organisation::whereIn('id', $organisationIds)->get()->toArray();
        $user = User::find(auth()->user()->id);

        if ($user->hasRole('external')) {
            $this->organisations = Organisation::whereIn('id', [$user->organisation->id])->get()->toArray();
        }

        $additionalOrganisations = [
            [
                'id' => 0,
                'name' => 'ALL',
                'created_at' => '2020-01-01 00:00:00',
                'updated_at' => '2020-01-01 00:00:00'
            ],

        ];
        $this->organisations = array_merge($additionalOrganisations, $this->organisations);

        $this->selectedOrganisation =   $this->organisations[0];
        $crops = CoreFunctions::getCropsWithNull();
        $this->crops = collect();
        foreach ($crops as $crop) {
            if ($crop === null) {
                $this->crops->push(
                    [
                        'name' => 'All Crops',
                        'value' => null
                    ]
                );
            } else {
                $this->crops->push(
                    [
                        'name' => $crop,
                        'value' => $crop
                    ]
                );
            }
        }


        $this->selectedCrop = $this->crops->where('value', null)->first();
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
}
