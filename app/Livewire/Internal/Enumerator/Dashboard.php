<?php

namespace App\Livewire\Internal\Enumerator;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class Dashboard extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.internal.enumerator.dashboard');
    }
}
