<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard2Charts extends Component
{
    use LivewireAlert;
    public $data = [];
    public $showContent = false;
    public $name = null;
    public $financialYears = [];
    public $selectedReportYear = 1;

    #[On('showCharts')]
    public function showVisuals()
    {

        $this->showContent = true;
    }

    public function save() {}

    public function mount() {}


    public function render()
    {
        return view('livewire.dashboard2-charts');
    }
}
