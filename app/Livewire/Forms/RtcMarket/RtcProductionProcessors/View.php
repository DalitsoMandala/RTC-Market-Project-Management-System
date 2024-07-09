<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exceptions\UserErrorException;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionProcessor;
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
        return view('livewire.forms.rtc-market.rtc-production-processors.view');
    }
}
