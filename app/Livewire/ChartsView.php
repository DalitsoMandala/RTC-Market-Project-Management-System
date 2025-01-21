<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChartsView extends Component
{
    use LivewireAlert;
    public $data;


    public function save() {}

    public function mount() {}

    #[On('updateChartData')]
    public function updateCharts($data)
    {
        // Access parameters as needed
        $this->data = $data;
    }


    public function render()
    {
        return view('livewire.charts-view');
    }
}
