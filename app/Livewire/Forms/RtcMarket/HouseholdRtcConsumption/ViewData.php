<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Livewire\WithPagination;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Illuminate\Bus\Batch;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Models\HouseholdRtcConsumption;
use App\Jobs\ProcessHouseholdRtcConsumption;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    use WithPagination;
    public $batch_no;
    public $job_id;
    public $data = [];
    public bool $loadingData = true;

    public $batchId;

    public $finishedLoading = false;
    public function mount($batch = null)
    {
        if ($batch) {
            $this->batch_no = $batch;
        }

        //  $this->load();


    }

    public function load()
    {

        // $batch = Bus::batch([
        //     new ProcessHouseholdRtcConsumption($this->batch_no)
        // ])->before(function (Batch $batch) {
        //     // The batch has been created but no jobs have been added...

        // })->progress(function (Batch $batch) {
        //     // A single job has completed successfully...
        // })->then(function (Batch $batch) {
        //     // All jobs completed successfully...
        // })->catch(function (Batch $batch, Throwable $e) {

        // })->finally(function (Batch $batch) {
        //     // The batch has finished executing...

        // })

        //     ->dispatch();
        // $this->batchId = $batch->id;

    }


    #[On('checking')]
    public function checkJobStatus()
    {
        // $this->loadingData = true;
        // $jobStatus = Bus::findBatch($this->batchId);

        // if ($jobStatus && $jobStatus->finished()) {

        //     $data = cache()->get('hrc_batch', []);
        //     $this->data = $data;
        //     // $this->dispatch('loaded-data', data: $this->data);
        //     $this->loadingData = false;
        // }
    }

    public function readCache()
    {
        //$finished = Bus::findBatch($this->job_id);

        // $data = cache()->get('hrc_batch', []);

        // if (!empty($data)) {


        //     $this->data = $data;
        //     $this->dispatch('loaded-data', data: $this->data);
        //     $this->loadingData = false;
        // }

    }


    public function render()
    {

        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data', [

        ]);
    }
}
