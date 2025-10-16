<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateFormsExportJob;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Forms extends Component
{
   public $downloading = false;
    public $downloadReady = false;
    public $downloadUrl = null;

    public function downloadForms()
    {
        $this->downloading = true;
        $this->downloadReady = false;
        $this->downloadUrl = null;

        if(Cache::has("forms_export_" . auth()->user()->id)){
            $this->pollForDownload();
            return;
        }
        // Dispatch the job
        GenerateFormsExportJob::dispatch(auth()->user()->id);
    }

    public function pollForDownload()
    {
        if ($this->downloading) {
            $cacheKey = "forms_export_" . auth()->user()->id;
            $fileName = Cache::get($cacheKey);

            if ($fileName) {
                $this->downloading = false;
                $this->downloadReady = true;
                $this->downloadUrl = asset('storage/exports/' . $fileName);

                // Clear the cache after successful download
                Cache::forget($cacheKey);
            }
        }
    }

    public function downloadFile()
    {
        if ($this->downloadUrl) {
            $fileName = basename($this->downloadUrl);
            $filePath = storage_path('app/public/exports/' . $fileName);

            if (file_exists($filePath)) {
                return response()->download($filePath)->deleteFileAfterSend(true);
            }
        }

        session()->flash('error', 'Download file not found.');
        return null;
    }
    public function render()
    {
        return view('livewire.internal.cip.forms');
    }
}
