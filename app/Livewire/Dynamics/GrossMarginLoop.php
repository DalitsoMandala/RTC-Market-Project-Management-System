<?php

namespace App\Livewire\Dynamics;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class GrossMarginLoop extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.dynamics.gross-margin-loop');
    }
}
