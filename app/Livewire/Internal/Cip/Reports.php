<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Indicator;
use App\Models\Project;
use Livewire\Attributes\On;
use Livewire\Component;

class Reports extends Component
{

    public $projects;
    //#[Validate('required')]
    public $selectedProject;

    public $indicators;
    // #[Validate('required')]
    public $selectedIndicators = [];
    // #[Validate('required')]
    public $starting_period;
    //  #[Validate('required')]
    public $ending_period;

    public $filtered;
    public function mount()
    {
        $this->projects = Project::get();
        $this->indicators = Indicator::get();

    }

    public function filter()
    {

        $this->filtered = [
            'project_id' => $this->selectedProject,
            'indicators' => $this->selectedIndicators,
            'start_date' => $this->starting_period,
            'end_date' => $this->ending_period,
        ];

        $this->dispatch('filtered-data', $this->filtered);
    }

    #[On('reset-filters')]
    public function resetFilters()
    {
        $this->selectedProject = null;
        $this->selectedIndicators = [];
        $this->starting_period = null;
        $this->ending_period = null;
    }
    public function updated($property, $value)
    {

    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
