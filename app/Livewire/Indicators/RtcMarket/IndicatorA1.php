<?php

namespace App\Livewire\Indicators\RtcMarket;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Helpers\rtc_market\indicators\A1;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Helpers\rtc_market\indicators\indicator_A1;

class IndicatorA1 extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;
    public $cropCount = [];
    public $indicator_no;
    public $indicator_id, $project_id;
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
    public $total;
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

        $organisation = auth()->user()->organisation;
        $class = Indicator::find($this->indicator_id)->class()->first();
        $newClass = null;
        if (auth()->user()->hasAnyRole('admin') || (auth()->user()->hasAnyRole('cip') && auth()->user()->hasAnyRole('organiser'))) {
            $newClass = new $class->class();
        } else {
            $newClass = new $class->class(organisation_id: $organisation->id);
        }
        try {
            $this->data = $newClass->getDisaggregations();//
            $this->total = $this->data['Total'];
            $this->dataByCrop = [
                'Farmers' => $newClass->RtcActorByCrop('FARMER'),
                'Processors' => $newClass->RtcActorByCrop('PROCESSOR'),
                'Traders' => $newClass->RtcActorByCrop('TRADER'),
            ];

            $this->dataByActorMales = [
                'Farmers' => (int) $newClass->RtcActorBySex('MALE')['farmer'],
                'Processors' => (int) $newClass->RtcActorBySex('MALE')['processor'],
                'Traders' => (int) $newClass->RtcActorBySex('MALE')['trader'],
            ];

            $this->dataByActorFemales = [
                'Farmers' => (int) $newClass->RtcActorBySex('FEMALE')['farmer'],
                'Processors' => (int) $newClass->RtcActorBySex('FEMALE')['processor'],
                'Traders' => (int) $newClass->RtcActorBySex('FEMALE')['trader'],
            ];

            $this->dataBySex = [
                'males' => $this->dataByActorMales,
                'females' => $this->dataByActorFemales,

            ];
            $this->dataByActorYouth = [
                'Farmers' => (int) $newClass->RtcActorByAge('YOUTH')['farmer'],
                'Processors' => (int) $newClass->RtcActorByAge('YOUTH')['processor'],
                'Traders' => (int) $newClass->RtcActorByAge('YOUTH')['trader'],
            ];
            $this->dataByActorNotYouth = [
                'Farmers' => (int) $newClass->RtcActorByAge('NOT YOUTH')['farmer'],
                'Processors' => (int) $newClass->RtcActorByAge('NOT YOUTH')['processor'],
                'Traders' => (int) $newClass->RtcActorByAge('NOT YOUTH')['trader'],
            ];

            $this->dataByAge = [
                'youth' => $this->dataByActorYouth,
                'not_youth' => $this->dataByActorNotYouth,

            ];

        } catch (\Throwable $th) {

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
