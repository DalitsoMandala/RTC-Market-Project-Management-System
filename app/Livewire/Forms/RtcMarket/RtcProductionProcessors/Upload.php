<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;

use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\ImportError;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Traits\UploadDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use App\Traits\CheckProgressTrait;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorFollowUp;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RpmProcessorDomMarket;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\RtcProductionProcessor;
use App\Notifications\JobNotification;
use App\Models\RpmProcessorInterMarket;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\SheetImportException;
use App\Models\RpmProcessorConcAgreement;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\BatchDataAddedNotification;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImport;
use App\Imports\ImportFarmer\RtcProductionFarmersMultiSheetImport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;
use App\Imports\ImportProcessor\RtcProductionProcessorsMultiSheetImport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    use CheckProgressTrait;
    use UploadDataTrait;
    #[Validate('required')]
    public $upload;
    public $variable;
    public $rowId;
    public $selectedIndicator;
    public $selectedMonth;

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
    public $form_name;

    public function save() {}

    public function submitUpload()
    {

        try {
            $this->validate();
        } catch (Throwable $e) {
            $this->dispatch('errorRemove');
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }
        try {
            //code...

            $userId = auth()->user()->id;

            if ($this->upload) {

                $name = 'rpmp' . time() . '.' . $this->upload->getClientOriginalExtension();
                $directory = 'public/imports';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                $this->upload->storeAs($directory, $name);
                $path = storage_path('app/public/imports/' . $name);




                try {



                    Excel::import(new RtcProductionProcessorsMultiSheetImport(cacheKey: $this->importId, filePath: $path, submissionDetails: [

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


                    session()->flash('error', $th->getMessage());
                    Log::error($th);
                    $this->redirect(url()->previous());
                }
            }
        } catch (\Exception $th) {
            //throw $th;

            session()->flash('error', 'Something went wrong!');
            Log::error($th);
            $this->redirect(url()->previous());
        }

        $this->removeTemporaryFile();
    }



    public function send()
    {
        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('external')) {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('external-submissions') . '#batch-submission');
        } else if ($user->hasAnyRole('staff')) {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('cip-staff-submissions') . '#batch-submission');
        } else {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('cip-submissions') . '#batch-submission');
        }
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
        $this->currentRoute =  url()->current();
    }
    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new RtcProductionProcessorsMultiSheetExport(true), 'rtc_production_marketing_processors_template.xlsx');
    }

    public function removeTemporaryFile()
    {
        // Get the temporary file path
        if ($this->upload) {
            $temporaryFilePath = $this->upload->getRealPath();

            // Check if the file exists and delete it
            if (file_exists($temporaryFilePath)) {
                try {
                    unlink($temporaryFilePath);
                } catch (\Exception $e) {
                    // Handle the exception (e.g., log the error)
                    Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }
        }
    }


    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-production-processors.upload');
    }
}
