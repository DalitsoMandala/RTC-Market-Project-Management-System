<?php

namespace App\Livewire;

use Throwable;
use App\Jobs\Mapper;
use Livewire\Component;
use Illuminate\Bus\Batch;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ReportingComponent extends Component
{
    use LivewireAlert;

    public $data = [];
    public bool $loadingData = true;

    public function load()
    {
        $batch = Bus::batch([
            new Mapper()
        ])->before(function (Batch $batch) {
            // The batch has been created but no jobs have been added...

        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...

        })

            ->dispatch();
    }

    public function mount()
    {

        $this->load();
    }

    public function readCache()
    {
        $this->loadingData = true;
        $data = cache()->get('report_', []);

        if (!empty($data)) {

            $this->loadingData = false;
            $this->data = $data;

        }

    }


    public function render(
    ) {


        return view('livewire.reporting-component', [
            'data' => ''
        ]);
    }
}
