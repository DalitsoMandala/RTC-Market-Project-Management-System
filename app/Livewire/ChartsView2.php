<?php

namespace App\Livewire;

use App\Models\MarketData;
use App\Models\MarketDataReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Computed;

class ChartsView2 extends Component
{
    use LivewireAlert;
    public $data = [];

    #[On('update-chart')]
    public function updateCharts($data)
    {

        // Access parameters as needed
        $this->data = $data;
    }

    public function render()
    {
        return view('livewire.charts-view2');
    }
}
