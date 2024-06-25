<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $batch_no;
    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data');
    }
}
