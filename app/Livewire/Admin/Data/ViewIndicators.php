<?php

namespace App\Livewire\Admin\Data;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Services\IndicatorService;
use App\Traits\ViewIndicatorTrait;
use App\Models\ReportingPeriodMonth;

class ViewIndicators extends Component
{
    use ViewIndicatorTrait;
    public function render()
    {
        return view('livewire.admin.data.view-indicators');
    }
}
