<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChartsView3 extends Component
{
    use LivewireAlert;
    public $data = [];
    public $farmingCostData = [];
    public $grossCategories = [];
    public $grossMarginCalculations = [];
    public $grossMarginVarieties = [];
    public $farmingCostCategories = [];
    public $farmingCostCalculations = [];
    public $farmingCostVarieties = [];

    public function save() {}

    public function mount() {}


    public function render()
    {
        return view('livewire.charts-view3');
    }
}
