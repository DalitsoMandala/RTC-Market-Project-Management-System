<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Models\Indicator;

use App\Helpers\rtc_market\indicators\A1;

use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Indicator415 extends Component
{
    use LivewireAlert;
    #[Validate('required')]

    public $rowId;
    public $data = [];
    public $indicator_no;
    public $indicator_id, $project_id;
    public $indicator_name;

    public $total;




    public function calculations()
    {

        $organisation = auth()->user()->organisation;
        $class = Indicator::find($this->indicator_id)->class()->first();
        $newClass = null;
        if (auth()->user()->hasAnyRole('admin')) {
            $newClass = new $class->class();
        } else {
            $newClass = new $class->class(organisation_id: $organisation->id);
        }



        try {
            $this->data = $newClass->getDisaggregations();//
            $this->total = $this->data['Total'];


        } catch (\Throwable $th) {

        }

    }
    public function mount()
    {
        $this->calculations();

    }

    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator415');
    }
}
