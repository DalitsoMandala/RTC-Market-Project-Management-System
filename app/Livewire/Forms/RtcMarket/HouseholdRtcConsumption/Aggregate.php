<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class Aggregate extends Component
{
        use LivewireAlert;
   #[Validate('required')]
public $variable;
public $rowId;

    public function setData($id){
$this->resetErrorBag();

    }

 public function save(){

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
        return view('livewire.forms.rtc-market.household-rtc-consumption.aggregate');
    }
}
