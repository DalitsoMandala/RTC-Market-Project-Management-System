<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Project;
use Livewire\Component;

class Reports extends Component
{

    public function mount()
    {
        $this->projects = Project::get();
    }
    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
