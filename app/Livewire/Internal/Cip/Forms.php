<?php
namespace App\Livewire\Internal\Cip;

use App\Jobs\GenerateFormsExportJob;
use App\Models\Form;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class Forms extends Component
{
    public $downloading   = false;
    public $downloadReady = false;
    public $downloadUrl   = null;
    public $name          = '';

    public $project_id = '';

    public $editing = false;

    public $formId = null;

    public $projects = [];
    public function createForm()
    {
        $this->reset([
            'name',
            'project_id',
            'formId',
        ]);

        $this->editing = false;

        $this->resetValidation();

        $this->dispatch('showModal', [
            'name' => 'form-modal',
        ]);
    }

    public function save()
    {
        $this->validate([
            'name'       => [
                'required',
                'string',
                'max:255',
            ],

            'project_id' => [
                'required',
                'exists:projects,id',
            ],
        ]);

        if ($this->editing) {

            $form = Form::findOrFail($this->formId);

            $form->update([
                'name'       => $this->name,
                'project_id' => $this->project_id,
                'type'       => 'routine/recurring',
            ]);

            $message = 'Form updated successfully.';

        } else {

            $nameToUpperCase = strtoupper($this->name);

            Form::create([
                'name'       => $nameToUpperCase,
                'project_id' => $this->project_id,
                'type'       => 'routine/recurring',
                'slug'       => Str::slug(strtolower($nameToUpperCase)),
            ]);

            $message = 'Form created successfully.';
        }

        session()->flash('success', $message);

        $this->reset([
            'name',
            'project_id',
            'formId',
        ]);

        $this->dispatch('hideModal');
        $this->editing = false;
    }
    public function mount()
    {
        $this->projects = Project::orderBy('name')->get();
    }
    public function downloadForms()
    {
        $this->downloading   = true;
        $this->downloadReady = false;
        $this->downloadUrl   = null;

        if (Cache::has("forms_export_" . auth()->user()->id)) {
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
                $this->downloading   = false;
                $this->downloadReady = true;
                $this->downloadUrl   = asset('storage/exports/' . $fileName);

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

                $this->downloadReady = false;
                return response()->download($filePath)->deleteFileAfterSend(true);
            }

        }

        session()->flash('error', 'Download file not found.');

        $this->downloadReady = false;
        return null;
    }
    public function render()
    {
        return view('livewire.internal.cip.forms');
    }
}
