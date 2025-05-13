<?php

namespace App\Livewire\Indicators\RtcMarket;

use Livewire\Component;

use App\Models\Indicator;

use App\Models\SystemReport;
use App\Models\SystemReportData;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Helpers\rtc_market\indicators\A1;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class IndicatorB1 extends Component
{
    use LivewireAlert;
    use \App\Traits\ViewIndicatorCalculationsTrait;


    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator-b1');
    }
}
