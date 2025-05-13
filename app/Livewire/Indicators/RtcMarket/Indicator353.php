<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Models\Indicator;

use App\Helpers\rtc_market\indicators\A1;

use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\SystemReport;
use App\Models\SystemReportData;

class Indicator353 extends Component
{
        use LivewireAlert;
    use \App\Traits\ViewIndicatorCalculationsTrait;

    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator353');
    }
}
