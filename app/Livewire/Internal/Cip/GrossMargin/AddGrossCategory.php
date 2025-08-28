<?php

namespace App\Livewire\Internal\Cip\GrossMargin;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class AddGrossCategory extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.internal.cip.gross-margin.add-gross-category');
    }
}
