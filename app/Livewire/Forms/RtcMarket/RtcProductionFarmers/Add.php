<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Add extends Component
{
    use LivewireAlert;

    public $inputs = [];
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        $this->resetErrorBag();
        try {

            $this->alert('success', 'Successfully updated');

        } catch (\Throwable $th) {
            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }
        $this->reset();
    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.add');
    }
}