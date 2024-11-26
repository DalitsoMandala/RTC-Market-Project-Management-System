<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Jobs\RandomNames;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\ImportError;
use App\Models\JobProgress;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Models\JobProgressCheck;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Notifications\JobNotification;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\SheetImportException;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\BatchDataAddedNotification;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Exports\HouseholdExport\HouseholdRtcConsumptionTemplateExport;
use App\Imports\HouseholdImport\HouseholdRtcConsumptionMultiSheetImport;



class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
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
    public $showReport = false;
    public $targetSet = false;
    public $targetIds = [];
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

                $name = 'hh_' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs('public/imports', $name);
                $path = storage_path('app/public/imports/' . $name);



                // Queue the import with Laravel Excel's queueable functionality

                try {



                    Excel::import(new HouseholdRtcConsumptionMultiSheetImport(cacheKey: $this->importId, filePath: $path, submissionDetails: [

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
                        'batch_no' => $this->importId


                    ]), $path);
                    $this->checkProgress();
                } catch (ExcelValidationException $th) {


                    session()->flash('error', $th->getMessage());
                    Log::error($th);
                    return redirect()->to(url()->previous());
                }
            }
        } catch (\Exception $th) {



            session()->flash('error', 'Something went wrong!');
            Log::error($th);
            return redirect()->to(url()->previous());
        }

        $this->removeTemporaryFile();
    }

    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }
    public function checkProgress()
    {
        $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

        $this->progress = $jobProgress ? $jobProgress->progress : 0;
        $this->importing = true;
        $this->importingFinished = false;


        if ($this->progress == 100) {
            $this->importing = false;
            $this->importingFinished = true;


            $this->importId = Uuid::uuid4()->toString(); // change key

            if ($jobProgress->status == 'failed') {

                session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);
                return redirect()->to(url()->previous());
            } else if ($jobProgress->status == 'completed') {

                $user = User::find(auth()->user()->id);

                if ($user->hasAnyRole('external')) {
                    session()->flash('success', 'Successfully submitted!');
                    return redirect(route('external-submissions') . '#batch-submission');
                } else if ($user->hasAnyRole('staff')) {
                    session()->flash('success', 'Successfully submitted!');
                    return redirect(route('cip-staff-submissions') . '#batch-submission');
                } else {
                    session()->flash('success', 'Successfully submitted!');
                    return redirect(route('cip-internal-submissions') . '#batch-submission');
                }
            }


        }
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
            return redirect(route('cip-internal-submissions') . '#batch-submission');
        }


    }





    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id, $uuid)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);
        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);
        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();

            $target = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)

                ->get();
            $user = User::find(auth()->user()->id);


            $targets = $target->pluck('id');
            $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)
                ->whereHas('submissionTarget', function ($query) use ($targets) {
                    $query->whereIn('submission_target_id', $targets);
                })
                ->get();
            $this->targetIds = $target->pluck('id')->toArray();


            if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

                $this->openSubmission = true;
                $this->targetSet = true;
            } else {
                $this->openSubmission = false;
                $this->targetSet = false;
            }
        }



        $this->importId = Uuid::uuid4()->toString();
    }
    public function downloadTemplate()
    {
        //  $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new HouseholdRtcConsumptionTemplateExport(true), 'household_rtc_consumption_template.xlsx');
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
                    Log::channel('system')->error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }
        }
    }



    public function render()
    {

        return view('livewire.forms.rtc-market.household-rtc-consumption.upload');
    }
}
