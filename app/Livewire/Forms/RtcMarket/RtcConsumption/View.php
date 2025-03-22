<?php

namespace App\Livewire\Forms\RtcMarket\RtcConsumption;

use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\SchoolConsumptionImport\SrcImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\SchoolRtcConsumption;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\BatchDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class View extends Component
{
    use LivewireAlert;

    public $batch_no;

    public function mount($batch = null)
    {
        if ($batch) {
            $this->batch_no = $batch;
        }

    }
    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-consumption.view');
    }
}