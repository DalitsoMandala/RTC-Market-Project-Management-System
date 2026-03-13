<?php

namespace App\Livewire\Internal\Cip;

use App\Jobs\ReportJob;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Livewire\Attributes\On;
use Livewire\Component;

class Reports extends Component
{
    public $progress = 0;

    public bool $loading = false;

    public function mount()
    {
        $status = ReportStatus::first();

        if ($status) {
            $this->progress = $status->progress;
            $this->loading = $status->status === 'processing';
        }
    }

    public function load()
    {
        $status = ReportStatus::first();

        if (!$status) {
            $status = ReportStatus::create([
                'status' => 'processing',
                'progress' => 0
            ]);
        }

        $status->update([
            'status' => 'processing',
            'progress' => 0
        ]);

        $this->loading = true;
        $this->progress = 0;

        Artisan::call('update:information');
    }

    public function reload()
    {
        $this->loading = true;
    }




    public function checkProgress()
    {
        $status = ReportStatus::first();

        if (!$status) {
            return;
        }

        $this->progress = $status->progress;

        if ($status->status === 'completed') {
            $this->loading = false;
            $this->dispatch('report-completed');
        }
    }

    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
