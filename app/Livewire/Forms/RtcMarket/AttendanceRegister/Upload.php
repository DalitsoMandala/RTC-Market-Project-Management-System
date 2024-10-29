<?php

namespace App\Livewire\Forms\RtcMarket\AttendanceRegister;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class Upload extends Component
{
        use LivewireAlert;


    public function save(){


    }

    public function mount(){

    }


    public function render()
    {
        return view('livewire.forms.rtc-market.attendance-register.upload');
    }
}
