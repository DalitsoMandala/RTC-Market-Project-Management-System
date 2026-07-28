<?php
namespace App\Livewire\Internal\Cip;

use App\Models\ReportStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Reports extends Component
{
    public $progress = 0;

    public bool $loading = false;

    public function mount()
    {

    }

    public function load()
    {
        $status = ReportStatus::first();

        if (! $status) {
            $status = ReportStatus::create([
                'status'   => 'pending',
                'progress' => 0,
            ]);
        }

        if ($status->status === 'processing') {
            $this->loading = true;
        } else {
            Artisan::call('update:information');
            $this->loading = true;
        }

        if (Cache::has('report_progress_error') && Cache::get('report_progress_error') !== null) {
            $this->dispatch('report-error', Cache::get('report_progress_error'));
        }
    }

    public function reload()
    {
        $this->loading = true;
    }

    public function checkProgress()
    {
        $status = ReportStatus::first();

        if (! $status) {
            Log::error('ReportStatus not found', [
                'context' => 'Reports Livewire Component',
            ]);
            return;
        }

        $this->progress = $status->progress;

        if ($status->status === 'completed') {
            $this->loading = false;
            $this->dispatch('report-updated');
        }
    }

    public function render()
    {
        return view('livewire.internal.cip.reports');
    }
}
