<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exports\rtcmarket\HrcExport;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ViewData extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        $this->reset();
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new HrcExport, 'household_rtc_consumption_template_' . $time . '.xlsx');

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data');
    }
}
