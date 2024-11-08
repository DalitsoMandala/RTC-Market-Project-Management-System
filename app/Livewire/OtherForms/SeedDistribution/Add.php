<?php

namespace App\Livewire\OtherForms\SeedDistribution;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class Add extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.other-forms.seed-distribution.add');
    }
}
