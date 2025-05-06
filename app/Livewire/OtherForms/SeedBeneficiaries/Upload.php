<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

use Throwable;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Traits\UploadDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use App\Traits\CheckProgressTrait;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Exports\SeedBeneficiariesExport;
use App\Imports\SeedBeneficiariesImport;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Upload extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    use CheckProgressTrait;
    use UploadDataTrait;
    public $upload;
    public $variable;
    public $rowId;
    public $selectedIndicator;
    public $selectedMonth;
public $form_name;
    public $selectedFinancialYear;

    public $selectedProject;

    public $selectedForm;

    public $submissionPeriodId;

    public $openSubmission = false;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public $queue = false;

    public $targetSet = false;
    public $targetIds = [];
    public $currentRoute;
    public function submitUpload()
    {

        try {

            $this->validate([
                'upload' => 'required|file|mimes:xlsx,csv',
            ]);
            $name = 'seed' . time() . '.' . $this->upload->getClientOriginalExtension();
            $directory = 'public/imports';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $this->upload->storeAs($directory, $name);
            $path = storage_path('app/public/imports/' . $name);
            try {


                Excel::import(new SeedBeneficiariesImport(cacheKey: $this->importId, filePath: $path, submissionDetails: [

                    'submission_period_id' => $this->submissionPeriodId,
                    'organisation_id' => Auth::user()->organisation->id,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'period_month_id' => $this->selectedMonth,
                    'form_id' => $this->selectedForm,
                    'user_id' => Auth::user()->id,
                    'batch_type' => 'batch',
                    'table_name' => 'household_rtc_consumption',
                    'is_complete' => 1,
                    'file_link' => $name,
                    'batch_no' => $this->importId,
                    'route' => $this->currentRoute



                ]), $path);
                $this->checkProgress();
            } catch (ExcelValidationException $th) {

                $this->reset('upload');
                session()->flash('error', $th->getMessage());
                Log::error($th);
            }
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }



        // Use Excel import (Assumes you have set up an Import for SeedBeneficiaries)


        session()->flash('message', 'Batch uploaded successfully.');
        $this->reset('upload'); // Clear the file input after upload
    }

    public function downloadTemplate()
    {
        // Path to your template file
        return Excel::download(new SeedBeneficiariesExport(true), 'seed_beneficiaries_template.xlsx');
    }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        //import ID
        $this->importId = Uuid::uuid4()->toString();
        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();
    }
    public function send()
    {

        session()->flash('success', 'File uploaded successfully! <a href="' . $this->routePrefix . '/seed-beneficiaries">View Submission here</a>');
    }

    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.other-forms.seed-beneficiaries.upload');
    }
}
