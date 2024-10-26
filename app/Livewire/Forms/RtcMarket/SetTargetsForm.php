<?php

namespace App\Livewire\Forms\RtcMarket;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class SetTargetsForm extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.forms.rtc-market.set-targets-form');
    }
}
