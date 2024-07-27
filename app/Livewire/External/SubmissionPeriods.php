<?php

namespace App\Livewire\External;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class SubmissionPeriods extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.external.submission-periods');
    }
}
