<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Models\FinancialYear;
use App\Models\SystemReport;
use App\Models\SystemReportData;

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
    use \App\Traits\ViewIndicatorCalculationsTrait;
    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator-a1');
    }
}