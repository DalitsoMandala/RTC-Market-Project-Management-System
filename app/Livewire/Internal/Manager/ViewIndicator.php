<?php

namespace App\Livewire\Internal\Manager;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use Livewire\Attributes\Validate;
use App\Services\IndicatorService;
use App\Traits\ViewIndicatorTrait;
use App\Models\ReportingPeriodMonth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ViewIndicator extends Component
{
    use ViewIndicatorTrait;
    public function render()
    {
        return view('livewire.internal.manager.view-indicator');
    }
}
