<?php

namespace App\Livewire\External;

use App\Helpers\rtc_market\indicators\A1;
use App\Models\Indicator;
use App\Models\Project;
use data;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Dashboard extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;
    public $data;
    public $project;
    public $indicatorCount;
    public function setData($id)
    {
        $this->resetErrorBag();

    }

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
        return view('livewire.external.dashboard',[
            'projects' => Project::get(),
        ]);
    }
}