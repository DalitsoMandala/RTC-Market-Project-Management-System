<?php

namespace App\Livewire\External;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Services\IndicatorService;
use App\Traits\ViewIndicatorTrait;
use App\Models\ReportingPeriodMonth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ViewIndicator extends Component
{

    use ViewIndicatorTrait;
    public function render()
    {
        return view('livewire.external.view-indicator');
    }
}
