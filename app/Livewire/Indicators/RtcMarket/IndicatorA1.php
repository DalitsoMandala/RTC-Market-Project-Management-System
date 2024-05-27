<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Helpers\rtc_market\indicators\A1;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Lazy]
class IndicatorA1 extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId, $data;
    public $cropCount = [];
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

    public function cropCountData($actor_type = null)
    {
        $a1 = new A1();
        $cropCounts = $a1->cropCount($actor_type);

        return collect($cropCounts);

    }
    public function mount()
    {

        $this->data = [

            // 'cropCount' => $this->cropCountData(),
            'cropCountFarmer' => $this->cropCountData('FARMER'),
            'cropCountProcessor' => $this->cropCountData('PROCESSOR'),
            'cropCountTrader' => $this->cropCountData('TRADER'),
        ];

    }
    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator-a1');
    }
}
