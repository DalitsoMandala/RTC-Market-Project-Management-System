<?php

namespace App\Livewire\Internal\Cip;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Services\IndicatorService;
use App\Traits\ViewIndicatorTrait;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;

class ViewIndicators extends Component
{
    use ViewIndicatorTrait;
    public function render()
    {
        return view('livewire.internal.cip.view-indicators');
    }
}
