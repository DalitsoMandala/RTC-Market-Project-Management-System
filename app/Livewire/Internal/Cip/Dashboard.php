<?php

namespace App\Livewire\Internal\Cip;

use App\Helpers\rtc_market\indicators\A1;
use App\Models\Indicator;
use App\Models\Project;
use Livewire\Component;

class Dashboard extends Component
{

    public $data;
    public $project;
    public $indicatorCount;
    public function mount()
    {
        $a1 = new A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->fill([
            'indicatorCount' => Indicator::count(),
        ]);
        $this->data = [
            'actors' => $a1->getDisaggregations(),
            'name' => $indicator->indicator_name,
        ];
    }

    public function render()
    {
        return view('livewire.internal.cip.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}
