<?php

namespace App\Livewire\Forms\RtcMarket\AttendanceRegister;

use App\Models\AttendanceRegister;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class View extends Component
{
    use LivewireAlert;


    public function save()
    {


    }

    public function mount()
    {

    }


    public function render()
    {
        $perPage = 10; // Number of items per page
        $records = AttendanceRegister::cursorPaginate(10);

        return view('livewire.forms.rtc-market.attendance-register.view', [
            'collection' => $records
        ]);
    }
}
