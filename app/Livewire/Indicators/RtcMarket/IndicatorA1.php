<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Helpers\rtc_market\indicators\A1;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class IndicatorA1 extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;
    public $cropCount = [];
    public $indicator_no;

    public $indicator_name;
    public $cassavaFarmerValue;

    public $data = [];
    public $dataByCrop = [];

    public $dataByActorMales = [];
    public $dataByActorFemales = [];

    public $dataBySex = [];
    public $dataByAge = [];
    public $dataByActorYouth = [];

    public $dataByActorNotYouth = [];
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

    public function calculations()
    {
        $a1 = new A1();
        try {
            $this->data = $a1->getDisaggregations();//

            $this->dataByCrop = [
                'Farmers' => $a1->RtcActorByCrop('FARMER'),
                'Processors' => $a1->RtcActorByCrop('PROCESSOR'),
                'Traders' => $a1->RtcActorByCrop('TRADER'),
            ];

            $this->dataByActorMales = [
                'Farmers' => (int) $a1->RtcActorBySex('MALE')['farmer'],
                'Processors' => (int) $a1->RtcActorBySex('MALE')['processor'],
                'Traders' => (int) $a1->RtcActorBySex('MALE')['trader'],
            ];

            $this->dataByActorFemales = [
                'Farmers' => (int) $a1->RtcActorBySex('FEMALE')['farmer'],
                'Processors' => (int) $a1->RtcActorBySex('FEMALE')['processor'],
                'Traders' => (int) $a1->RtcActorBySex('FEMALE')['trader'],
            ];

            $this->dataBySex = [
                'males' => $this->dataByActorMales,
                'females' => $this->dataByActorFemales,

            ];
            $this->dataByActorYouth = [
                'Farmers' => (int) $a1->RtcActorByAge('YOUTH')['farmer'],
                'Processors' => (int) $a1->RtcActorByAge('YOUTH')['processor'],
                'Traders' => (int) $a1->RtcActorByAge('YOUTH')['trader'],
            ];
            $this->dataByActorNotYouth = [
                'Farmers' => (int) $a1->RtcActorByAge('NOT YOUTH')['farmer'],
                'Processors' => (int) $a1->RtcActorByAge('NOT YOUTH')['processor'],
                'Traders' => (int) $a1->RtcActorByAge('NOT YOUTH')['trader'],
            ];

            $this->dataByAge = [
                'youth' => $this->dataByActorYouth,
                'not_youth' => $this->dataByActorNotYouth,

            ];

        } catch (\Throwable $th) {
            dd($th);
        }

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