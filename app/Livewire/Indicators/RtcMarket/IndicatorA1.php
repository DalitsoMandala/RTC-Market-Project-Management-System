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
    public $rowId;
    public $cropCount = [];

    public $cassavaFarmerValue;

    public $data = [];

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

    public function calculations($actor_type = null)
    {
        $a1 = new A1();
        $farmerCropCount = $a1->cropCountByRespondent('FARMER');
        $processorCropCount = $a1->cropCountByRespondent('PROCESSOR');
        $traderCropCount = $a1->cropCountByRespondent('TRADER');

        $this->data = [
            'farmerCropCount' => $farmerCropCount,
            'farmerCropCountTotal' => collect($farmerCropCount)->sum(),
            'farmerCropCountPercentage' => $a1->cropsPercentage($farmerCropCount),
            'processorCropCount' => $processorCropCount,
            'processorCropCountTotal' => collect($processorCropCount)->sum(),
            'processorCropCountPercentage' => $a1->cropsPercentage($processorCropCount),
            'traderCropCount' => $traderCropCount,
            'traderCropCountTotal' => collect($traderCropCount)->sum(),
            'traderCropCountPercentage' => $a1->cropsPercentage($traderCropCount),
            'cassavaCount' => $farmerCropCount['cassava_count'] +
            $processorCropCount['cassava_count'] + $traderCropCount['cassava_count'],

            'potatoCount' => $farmerCropCount['potato_count'] +
            $processorCropCount['potato_count'] + $traderCropCount['potato_count'],

            'swPotatoCount' => $farmerCropCount['sw_potato_count'] +
            $processorCropCount['sw_potato_count'] + $traderCropCount['sw_potato_count'],

            'cropCount' => $farmerCropCount['cassava_count'] +
            $processorCropCount['cassava_count'] + $traderCropCount['cassava_count'] +
            $farmerCropCount['potato_count'] +
            $processorCropCount['potato_count'] + $traderCropCount['potato_count'] + $farmerCropCount['sw_potato_count'] +
            $processorCropCount['sw_potato_count'] + $traderCropCount['sw_potato_count'],

        ];
    }
    public function mount()
    {
        $this->calculations();

    }
    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator-a1');
    }
}
