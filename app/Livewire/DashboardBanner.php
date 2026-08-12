<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class DashboardBanner extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.dashboard-banner');
    }
}
