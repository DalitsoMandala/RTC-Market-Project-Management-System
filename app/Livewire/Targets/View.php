<?php

namespace App\Livewire\Targets;

use App\Models\Indicator;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class View extends Component
{
    use LivewireAlert;

    public $routePrefix;
    public function save() {}
public $financialYear = 2;
    public function mount()
    {
        $this->routePrefix = Route::current()->getPrefix();

    }

    public function changeYear($year){
        $this->financialYear = $year;
    }


    public function render()
    {


        return view('livewire.targets.view');
    }
}
