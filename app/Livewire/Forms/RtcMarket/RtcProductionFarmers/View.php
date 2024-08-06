<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Bus\Batch;
use App\Models\Submission;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Jobs\ProcessRpmFarmers;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use Livewire\Attributes\Validate;
use App\Models\RpmFarmerDomMarket;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmFarmerInterMarket;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RpmFarmerConcAgreement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\BatchDataAddedNotification;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;

class View extends Component
{
    use LivewireAlert;
    use WithPagination;
    public $batch_no;
    public $data = [];
    public $farmers = [];
    public $farm_data = [];
    public $followUp_data = [];
    public $agreement_data = [];

    public $dom_data = [];

    public $inter_data = [];
    public bool $loadingData = true;

    public function mount($batch = null)
    {
        if ($batch) {
            $this->batch_no = $batch;
        }




    }


    public function load()
    {
        $this->loadingData = true;
        $batch = Bus::batch([
            new ProcessRpmFarmers($this->batch_no)
        ])->before(function (Batch $batch) {


        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, \Throwable $e) {

        })->finally(function (Batch $batch) {
            // The batch has finished executing...

        })

            ->dispatch();


    }


    public function readCache()
    {


        $data = cache()->get('rpmf_', []);

        if (!empty($data)) {


            $this->farm_data = $data['rpm_farmers'];
            $this->followUp_data = $data['rpm_farmers_flp'];
            $this->agreement_data = $data['rpm_farmers_agr'];
            $this->dom_data = $data['rpm_farmers_dom'];
            $this->inter_data = $data['rpm_farmers_inter'];


            $this->dispatch('loaded-data-farmer', data: $this->farm_data, followUp: $this->followUp_data, agreement: $this->agreement_data, dOm: $this->dom_data, inter: $this->inter_data);
            $this->loadingData = false;
        } else {
            $this->load();
        }

    }
    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.view');
    }
}