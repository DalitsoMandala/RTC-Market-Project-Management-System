<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Illuminate\Bus\Batch;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use App\Models\HouseholdRtcConsumption;
use App\Jobs\ProcessHouseholdRtcConsumption;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $batch_no;
    public $data = [];
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
            new ProcessHouseholdRtcConsumption($this->batch_no)
        ])->before(function (Batch $batch) {
            // The batch has been created but no jobs have been added...

        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {

        })->finally(function (Batch $batch) {
            // The batch has finished executing...

        })

            ->dispatch();

    }




    public function readCache()
    {


        $data = cache()->get('hrc_batch', []);

        if (!empty($data)) {
            $this->loadingData = false;
            $this->data = $data;
            $this->dispatch('loaded-data', data: $this->data);
        }

    }
    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data');
    }
}
